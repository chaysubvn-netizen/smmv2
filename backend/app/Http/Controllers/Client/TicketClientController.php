<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketClientController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::user()->id)
            ->where('domain', request()->getHost())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $tickets->getCollection()->transform(function ($ticket) {
            $ticket->messages = TicketMessage::where('ticket_id', $ticket->id)
                ->where('domain', request()->getHost())
                ->orderBy('created_at', 'desc')
                ->get();
            return $ticket;
        });

        $tickets->setCollection($tickets->getCollection()->sortByDesc('created_at'));

        return view('client.tickets.index', [
            'tickets' => $tickets,
        ]);
    }

    public function create(Request $request)
    {
        if (status_demo()) {
            return response()->json([
                'success' => false,
                'message' => 'Chức năng này không thể sử dụng trên website demo',
            ]);
        }
        $valid = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
            'order_id' => 'nullable|integer',
            'description' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        try {
            $ticket = new Ticket();
            $ticket->user_id = Auth::user()->id;
            $ticket->title = $request->title;
            $ticket->type = $request->type;
            $ticket->order_id = $request->order_id ?? null;
            $ticket->description = $request->description;
            $ticket->status = 'pending';
            $ticket->domain = $request->getHost();
            $ticket->save();

            $ticketMsg = new TicketMessage();
            $ticketMsg->ticket_id = $ticket->id;
            $ticketMsg->user_id = Auth::user()->id;
            $ticketMsg->message = $request->description;
            $ticketMsg->type = 'user';
            $ticketMsg->domain = $request->getHost();
            $ticketMsg->save();

            // Send notification to telegram bot
            $text = "<b>Ticket mới</b>\n";
            $text .= "<b>Người tạo:</b> " . Auth::user()->name . "\n";
            $text .= "<b>Tiêu đề:</b> " . $request->title . "\n";
            $text .= "<b>Loại:</b> " . $request->type . "\n";
            $text .= "<b>Đơn hàng:</b> " . ($request->order_id ? $request->order_id : 'Không có') . "\n";
            $text .= "<b>Nội dung:</b> " . $request->description . "\n";

            send_telegram($text);

            return response()->json([
                'success' => true,
                'message' => 'Tạo ticket thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function detail($id)
    {
   
        $ticket = Ticket::where('user_id', Auth::user()->id)
            ->where('domain', request()->getHost())
            ->where('id', $id)
            ->first();

        if (!$ticket) {
            return redirect()->route('tickets')->with('error', 'Không tìm thấy ticket');
        }

        $ticket->messages = TicketMessage::where('ticket_id', $ticket->id)
            ->where('domain', request()->getHost())
            ->orderBy('created_at', 'asc')
            ->get();

        return view('client.tickets.detail', [
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
        $valid = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        try {
            $ticket = Ticket::where('user_id', Auth::user()->id)
                ->where('domain', request()->getHost())
                ->where('id', $id)
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy ticket',
                ]);
            }

            $ticketMsg = new TicketMessage();
            $ticketMsg->ticket_id = $ticket->id;
            $ticketMsg->user_id = Auth::user()->id;
            $ticketMsg->message = strip_tags($request->message, '<a><b><i><u><strong><em><p><br><ul><li><ol><img>');
            $ticketMsg->type = 'user';
            $ticketMsg->domain = $request->getHost();
            $ticketMsg->save();

            // Send notification to telegram bot
            $text = "<b>Trả lời ticket</b>\n";
            $text .= "<b>Người trả lời:</b> " . Auth::user()->name . "\n";
            $text .= "<b>Tiêu đề:</b> " . $ticket->title . "\n";
            $text .= "<b>Nội dung:</b> " . $request->message . "\n";

            send_telegram($text);

            return response()->json([
                'success' => true,
                'message' => 'Trả lời ticket thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
