<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MainController extends Controller
{
    protected $oneSignalUrl;

    public function __construct()
    {
        $this->oneSignalUrl = 'https://onesignal.com/api/v1/notifications';
    }

    public function board()
    {
        $labels = [];
        $colors = [];
        $members = [];

        $dataMembers = User::where('role', '=', 'member')->get();
        foreach ($dataMembers as $member) {
            $labels[] = $member->name;
            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            $members[] = Task::whereYear('inviting_date', '=', date('Y'))
                ->where('user_id', '=', $member->id)
                ->count();
        }

        if (request()->ajax()) {
            $data = [
                'task' => [],
                'done' => [],
                'status' => [],
                'labels' => $labels,
                'colors' => $colors,
                'members' => $members,
            ];

            $status = [
                "invitation",
                "confirmation",
                "on site",
                "rectification",
                "system",
                "done"
            ];

            for ($i = 1; $i <= 12; $i++) {
                $task = Task::whereYear('inviting_date', '=', date('Y'))
                    ->whereMonth('inviting_date', '=', $i)
                    ->count();
                $done = Task::whereYear('inviting_date', '=', date('Y'))
                    ->whereMonth('inviting_date', '=', $i)
                    ->where('status', '=', 'done')
                    ->count();
                $data['task'][] = $task;
                $data['done'][] = $done;
            }

            foreach ($status as $key => $value) {
                $task = Task::whereYear('inviting_date', '=', date('Y'))
                    ->where('status', '=', $value)
                    ->count();
                $data['status'][] = $task;
            }

            return json_encode($data);
        }

        $data = [
            'total' => Task::whereYear('inviting_date', '=', date('Y'))->count(),
            'labels' => $labels,
            'colors' => $colors,
        ];

        return view('board', $data);
    }

    public function atp()
    {
        $user = Auth::user();

        if (request()->ajax()) {
            $tasks = Task::with('user');

            if ($user->role !== 'admin') {
                $tasks = Task::with('user')->where('user_id', '=', Auth::id());
            }

            return DataTables::of($tasks)->make();
        }

        if ($user->role !== 'admin') {
            return view('atp');
        } else {
            return view('atp-admin');
        }
    }

    public function formAtp($id)
    {
        $data = ['task' => null];

        if ($id) {
            $data['task'] = Task::find($id);
            if ($data['task']->file) {
                $data['task']->file_alt = 'excel.png';
            }
        }

        return view('form-atp', $data);
    }

    public function detailAtp($id)
    {
        $task = Task::with(['user', 'histories.user'])->find($id);

        if (!$task) {
            return back();
        }

        $task->status = strtoupper($task->status);
        $task->badge_class = $this->statusBadge($task->status);

        foreach ($task->histories as $history) {
            $history->status = strtoupper($history->status);
            $history->badge_class = $this->statusBadge($history->status);
        }

        return view('detail-atp', ['task' => $task]);
    }

    public function saveAtp(Request $request): RedirectResponse
    {
        $data = $request->all();
        $task = null;
        $validation = [
            'sonumb' => 'required',
            'site_name' => 'required',
            'site_id' => 'required',
            'operator' => 'required',
            'regency' => 'required',
            'file' => 'required',
        ];

        if ($data['id']) {
            $task = Task::with('user')->find($data['id']);
        } else {
            $validation['sonumb'] = 'required|unique:tasks,sonumb';
            $data['user_id'] = Auth::id();
            $data['status'] = 'invitation';
        }

        $request->validate($validation);

        if (
            $request->hasFile('file') &&
            $request->file('file')->isValid()
        ) {
            $filename = uniqid() . '.' . $request->file('file')->extension();
            $request->file('file')->storeAs('upload', $filename);
            $data['file'] = $filename;
            if (
                $task &&
                $task->file
            ) {
                Storage::delete('upload/' . $task->file);
            }
        } else {
            if (
                $task &&
                $task->file
            ) {
                $data['file'] = null;
                Storage::delete('upload/' . $task->file);
            }
        }

        if (
            $task &&
            $task->status === 'rectification'
        ) {
            $data['status'] = 'on site';
        }

        $savedTask = Task::updateOrCreate(['id' => $task ? $task->id : 0], $data);
        if ($savedTask) {
            $emailIds = [];
            $admins = User::where('role', '=', 'admin')->get();
            foreach ($admins as $admin) {
                $emailIds[] = 'user-' . $admin->id;
            }
            if (!$task) {
                TaskHistory::create([
                    'task_id' => $savedTask->id,
                    'user_id' => $savedTask->user_id,
                    'status' => $savedTask->status,
                ]);
                if (count($emailIds) > 0) {
                    $emailData = $this->statusEmail($savedTask->status);
                    $emailData['task'] = Task::with('user')->find($savedTask->id);
                    $emailData['task']->inviting_date = $this->dateFormat($emailData['task']->inviting_date);
                    $this->oneSignalNotifications($emailIds, $emailData);
                }
                // Mail::to($admins)->send(new TaskNotif($mailTask, $mailObject, $mailHeader, $mailFooter));
            } else {
                if ($task->status === 'rectification') {
                    if (count($emailIds) > 0) {
                        $emailData = $this->statusEmail('rectified');
                        $emailData['task'] = $task;
                        $emailData['task']->inviting_date = $this->dateFormat($emailData['task']->inviting_date);
                        $this->oneSignalNotifications($emailIds, $emailData);
                    }
                    // Mail::to($admins)->send(new TaskNotif($task, $mailObject, $mailHeader, $mailFooter));
                }
            }
            return redirect('/atp')->with('status', 'ATP ' . $data['sonumb'] . ($task ? ' updated.' : ' created.'));
        }

        return back()->withErrors([
            'error' => 'Something went wrong, please tray again later.',
        ])->withInput();
    }

    public function status(Request $request)
    {
        $data = $request->all();

        if (
            !$data['id'] ||
            !$data['status']
        ) {
            return back();
        }

        $task = Task::with('user')->find($data['id']);

        if (!$task) {
            return back();
        }

        if ($data['atp_date']) {
            $task->atp_date = $data['atp_date'];
        }

        $task->status = $data['status'];
        $task->save();

        $dataHistory = [
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'status' => $data['status'],
        ];

        if ($data['note']) {
            $dataHistory['note'] = $data['note'];
        }

        if (
            $request->hasFile('file') &&
            $request->file('file')->isValid()
        ) {
            $filename = uniqid() . '.' . $request->file('file')->extension();
            $request->file('file')->storeAs('upload', $filename);
            $dataHistory['file'] = $filename;
        }

        TaskHistory::create($dataHistory);

        $emailData = $this->statusEmail($data['status']);
        $emailData['task'] = $task;
        $emailData['task']->inviting_date = $this->dateFormat($emailData['task']->inviting_date);
        $emailIds = [];

        if ($emailData['object'] === 'Vendor') {
            $emailIds[] = 'user-' . $task->user->id;
        } else if ($emailData['object'] === 'CME') {
            $admins = User::where('role', '=', 'admin')->get();
            foreach ($admins as $admin) {
                $emailIds[] = 'user-' . $admin->id;
            }
        }

        if (count($emailIds) > 0) {
            $this->oneSignalNotifications($emailIds, $emailData);
        }

        return redirect('/atp')->with('status', 'ATP status changed to ' . $data['status']);
    }

    public function downloadAtp($id)
    {
        $task = Task::find($id);
        return Storage::download('upload/' . $task->file);
    }

    public function downloadHistory($id)
    {
        $history = TaskHistory::find($id);
        return Storage::download('upload/' . $history->file);
    }

    public function deleteAtp($id)
    {
        Task::find($id)->delete();
        TaskHistory::where('task_id', '=', $id)->delete();
        return redirect('/atp')->with('status', 'ATP deleted');
    }

    private function oneSignalNotifications($ids, $data)
    {
        return Http::withToken(env('ONESIGNAL_REST_API_KEY'))->post($this->oneSignalUrl, [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'template_id' => env('ONESIGNAL_TEMPLATE_ID'),
            'channel_for_external_user_ids' => 'email',
            'include_external_user_ids' => $ids,
            'custom_data' => $data
        ]);
    }

    private function dateFormat($date)
    {
        $formatted = Carbon::parse($date);
        $formatted->locale('id');
        return $formatted->isoFormat('dddd, D MMMM Y');
    }

    private function statusBadge($status)
    {
        $badge = '';
        switch ($status) {
            case 'INVITATION':
                $badge = 'bg-secondary';
                break;
            case 'CONFIRMATION':
                $badge = 'bg-primary';
                break;
            case 'ON SITE':
                $badge = 'bg-warning';
                break;
            case 'RECTIFICATION':
                $badge = 'bg-danger';
                break;
            case 'SYSTEM':
                $badge = 'bg-dark';
                break;
            case 'DONE':
                $badge = 'bg-success';
                break;
        }
        return $badge;
    }

    private function statusEmail($status)
    {
        $data = [
            'object' => '',
            'header' => '',
            'footer' => '',
        ];

        switch ($status) {
            case 'invitation':
                $data['object'] = 'CME';
                $data['header'] = 'undangan ATP (Inviting ATP), berikut :';
                $data['footer'] = 'Mohon dibantu konfirmasi untuk ATP site berikut';
                break;
            case 'confirmation':
                $data['object'] = 'Vendor';
                $data['header'] = 'konfirmasi ATP (Confirmation ATP), berikut :';
                $data['footer'] = 'Mohon disiapkan semua keperluan ketika ATP on site';
                break;
            case 'on site':
                $data['object'] = 'Vendor';
                $data['header'] = 'konfirmasi ATP on site, berikut :';
                $data['footer'] = 'Mohon disiapkan semua keperluan saat ATP on site';
                break;
            case 'rectification':
                $data['object'] = 'Vendor';
                $data['header'] = 'rektifikasi ATP (Rectification ATP), berikut :';
                $data['footer'] = 'Mohon dilakukan perbaikan selambatnya H+3 setelah ATP on site';
                break;
            case 'rectified':
                $data['object'] = 'CME';
                $data['header'] = 'rektifikasi ATP (Rectification ATP) berikut telah diperbaiki';
                $data['footer'] = 'Mohon dilakukan pemeriksaan ATP yang telah diperbaiki';
                break;
            case 'system':
                $data['object'] = 'Vendor';
                $data['header'] = 'ATP site berikut diterima';
                $data['footer'] = 'Mohon dilakukan input system sesuai dengan format yang telah disetujui';
                break;
            case 'done':
                $data['object'] = 'Vendor';
                $data['header'] = 'ATP site berikut telah selesai';
                $data['footer'] = 'Terimakasih telah melakukan serangkaian kegiatan ATP dengan memperhatikan keselamatan kerja';
                break;
        }

        return $data;
    }
}
