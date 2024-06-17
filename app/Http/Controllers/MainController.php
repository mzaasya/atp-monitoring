<?php

namespace App\Http\Controllers;

use App\Mail\TaskConfirmed;
use App\Mail\TaskNotif;
use App\Mail\TaskOnsite;
use App\Mail\TaskRectified;
use App\Mail\TaskRectify;
use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MainController extends Controller
{
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
        $task->badge_class = $this->_statusBadge($task->status);

        foreach ($task->histories as $history) {
            $history->status = strtoupper($history->status);
            $history->badge_class = $this->_statusBadge($history->status);
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
            $admins = User::where('role', '=', 'admin')->get();
            if (!$task) {
                TaskHistory::create([
                    'task_id' => $savedTask->id,
                    'user_id' => $savedTask->user_id,
                    'status' => $savedTask->status,
                ]);
                $mailTask = Task::with('user')->find($savedTask->id);
                Mail::to($admins)->send(new TaskNotif($mailTask));
            } else {
                if ($task->status === 'rectification') {
                    Mail::to($admins)->send(new TaskRectified($task));
                }
            }
            return redirect('/atp')->with('status', 'ATP ' . $data['sonumb'] . ($task ? ' updated.' : ' created.'));
        }

        return back()->withErrors([
            'error' => 'Something went wrong, please tray again later.',
        ])->withInput();
    }

    public function downloadAtp($id)
    {
        $task = Task::find($id);
        return Storage::download('upload/' . $task->file);
    }

    public function deleteAtp($id)
    {
        Task::find($id)->delete();
        TaskHistory::where('task_id', '=', $id)->delete();
        return redirect('/atp')->with('status', 'ATP deleted');
    }

    public function confirmAtp($id, $date)
    {
        $task = Task::find($id);
        $task->atp_date = $date;
        $task->status = 'confirmation';
        $task->save();

        $savedHistory = TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'status' => 'confirmation',
        ]);

        $history = TaskHistory::with(['user', 'task.user'])->find($savedHistory->id);
        Mail::to($history->task->user)->send(new TaskConfirmed($history));

        return redirect('/atp')->with('status', 'ATP confirmed');
    }

    public function onsiteAtp($id)
    {
        $task = Task::find($id);
        $task->status = 'on site';
        $task->save();

        $savedHistory = TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'status' => 'on site',
        ]);

        $history = TaskHistory::with(['user', 'task.user'])->find($savedHistory->id);
        Mail::to($history->task->user)->send(new TaskOnsite($history));

        return redirect('/atp')->with('status', 'ATP on site');
    }

    public function rectifyAtp($id, $note)
    {
        $task = Task::find($id);
        if ($note) {
            $task->note = $note;
        }
        $task->status = 'rectification';
        $task->save();

        $savedHistory = TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'status' => 'rectification',
        ]);

        $history = TaskHistory::with(['user', 'task.user'])->find($savedHistory->id);
        Mail::to($history->task->user)->send(new TaskRectify($history));

        return redirect('/atp')->with('status', 'ATP rectified');
    }

    public function systemAtp($id)
    {
        $task = Task::find($id);
        $task->status = 'system';
        $task->save();

        TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'status' => 'system',
        ]);

        return redirect('/atp')->with('status', 'ATP system');
    }

    public function doneAtp($id)
    {
        $task = Task::find($id);
        $task->status = 'done';
        $task->save();

        TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'status' => 'done',
        ]);

        return redirect('/atp')->with('status', 'ATP done');
    }

    private function _statusBadge($status)
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
}
