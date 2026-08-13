<?php

namespace App\Http\Controllers\Admin\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketAdminController extends Controller
{
    public function index()
    {
        return view('admin.tickets.index');
    }

    public function edit($id)
    {

        $ticket = Ticket::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$ticket) {
            return redirect()->route('admin.tickets.index')->with('error', 'Vé không tồn tại');
        }

        return view('admin.tickets.edit', [
            'ticket' => $ticket,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        $ticket = Ticket::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$ticket) {
            return redirect()->route('admin.tickets.index')->with('error', 'Vé không tồn tại');
        }
        $valid = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
            'order_id' => 'nullable',
            'status' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        $ticket->update([
            'title' => strip_tags($request->input('title')),
            'type' => $request->input('type'),
            'order_id' => $request->input('order_id'),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật vé thành công',
        ]);
    }

    public function detail($id)
    {
       
        $ticket = Ticket::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$ticket) {
            return redirect()->route('admin.tickets.index')->with('error', 'Vé không tồn tại');
        }

        $ticket->load('messages');

        return view('admin.tickets.detail', [
            'ticket' => $ticket,
        ]);
    }

    public function reply(Request $request, $id)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        $ticket = Ticket::where('domain', request()->getHost())->where('id', $id)->first();
        if (!$ticket) {
            return redirect()->route('admin.tickets.index')->with('error', 'Vé không tồn tại');
        }

        $valid = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        try{

            $ticketMsg = new TicketMessage();
            $ticketMsg->ticket_id = $ticket->id;
            $ticketMsg->user_id = $ticket->user_id;
            $ticketMsg->message = strip_tags($request->input('message'), '<a><b><i><u><strong><em><p><br><ul><li><ol><img>');
            $ticketMsg->type = 'admin';
            $ticketMsg->domain = request()->getHost();
            $ticketMsg->save();
            $ticket->update([
                'status' => 'processing',
            ]);

            // Gửi thông báo cho người dùng


            return response()->json([
                'success' => true,
                'message' => 'Trả lời vé thành công',
                'data' => $ticketMsg
            ]);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau',
            ]);
        }

    }
    public function messages(Request $request)
    {
        if (status_demo()) {
             // Optional: prevent access if desired, but view usually okay
        }

        $tickets = Ticket::where('domain', request()->getHost())
            ->with(['user'])
            ->latest('updated_at') // Sort by last update so active chats are on top
            ->get();

        $currentTicket = null;
        if ($request->has('id')) {
            $currentTicket = Ticket::where('domain', request()->getHost())
                ->where('id', $request->id)
                ->with(['messages', 'user'])
                ->first();

            if ($currentTicket && $currentTicket->user) {
                // Load extra counts for the specific user
                $currentTicket->user->loadCount(['orders', 'tickets']);
            }
        }

        return view('admin.tickets.messages', compact('tickets', 'currentTicket'));
    }

    public function fetchMessages(Request $request, $id)
    {
        $ticket = Ticket::where('id', $id)->first();
        
        if (!$ticket || $ticket->domain != request()->getHost()) {
            return response()->json(['success' => false, 'messages' => []]);
        }

        $lastId = (int) $request->input('last_id', 0);

        $messages = TicketMessage::where('ticket_id', $ticket->id)
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'status' => $ticket->status
        ]);
    }
}
