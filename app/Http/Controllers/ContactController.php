<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Helper: Admin သို့မဟုတ် Superadmin ဟုတ်မဟုတ် စစ်ဆေးရန်
    private function checkIsAdmin($user) {
        return in_array($user->role, ['admin', 'superadmin']);
    }

    // User ဘက်မှ Chat စတင်ရန်
    public function startChat() {
        $currentUser = auth()->user();

        // ကျောင်းသားအတွက် Session တွင် မိမိကိုယ်တိုင်၏ ID ကို receiver_id အဖြစ် သတ်မှတ်မည် (သို့မဟုတ် Shared Inbox ပုံစံအတွက်)
        session(['receiver_id' => $currentUser->id]); 
        return redirect()->route('chat.index');
    }

    // Admin/Superadmin အတွက် ကျောင်းသားစာရင်း ပြရန်
    public function list(Request $request) {
        if ($request->has('search') && !$request->filled('search')) {
            session()->forget('contact_list_last_query');
        } elseif ($request->filled('search')) {
            session(['contact_list_last_query' => $request->search]);
        } elseif (session()->has('contact_list_last_query')) {
            $request->merge(['search' => session('contact_list_last_query')]);
        }
        
        $search = $request->search; 

        // Admin ဘယ်သူဝင်ဝင် ကျောင်းသား (role = user) အားလုံးကို မြင်ရမည်
        $users = User::where('role', 'user')
                     ->when($search, function($query) use ($search) {
                         $query->where(function($q) use ($search) {
                             $q->where('name', 'like', '%' . $search . '%')
                                ->orWhere('roll_number', 'like', '%' . $search . '%');
                         });
                     })
                     ->with('unreadMessages')
                     ->with('lastMessage')
                     ->get(); 
                     
        return view('admin.contact.list', compact('users', 'search'));
    }

    // Chat စာမျက်နှာ ပြသရန်
    // public function index(Request $request, $receiverId = null) {
    //     $currentUser = auth()->user();

    //     $defaultReceiverId = 1;
    //     if ($this->checkIsAdmin($currentUser)) {
    //         $firstUser = User::where('role', 'user')->first();
    //         if ($firstUser) {
    //             $defaultReceiverId = $firstUser->id;
    //         }
    //     } else {
    //         $defaultReceiverId = $currentUser->id;
    //     }

    //     $id = $receiverId ?? session('receiver_id', $defaultReceiverId);
    //     session(['receiver_id' => $id]);

    //     $receiver = User::findOrFail($id);

    //     if (!$this->checkIsAdmin($currentUser) && $receiver->id !== $currentUser->id) {
    //         return redirect()->route('chat.index')->with('error', 'ကျောင်းသားအချင်းချင်း စကားပြောခွင့်မရှိပါ။');
    //     }

    //     // မဖတ်ရသေးသော မက်ဆေ့ချ်များကို ဖတ်ပြီးသားအဖြစ် ပြောင်းလဲခြင်း (Admin ဝင်လာလျှင် ကျောင်းသားဆီမှ လာသော မက်ဆေ့ချ်များကို ဖတ်ပြီးဟု သတ်မှတ်မည်)
    //     if ($this->checkIsAdmin($currentUser)) {
    //         Message::where('sender_id', $id)
    //                ->whereIn('receiver_id', User::whereIn('role', ['admin', 'superadmin'])->pluck('id'))
    //                ->where('is_read', 0)
    //                ->update(['is_read' => 1]);
    //     } else {
    //         Message::where('sender_id', '!=', $currentUser->id)
    //                ->where('receiver_id', $currentUser->id)
    //                ->where('is_read', 0)
    //                ->update(['is_read' => 1]);
    //     }

    //     // ကျောင်းသားတစ်ဦးချင်းစီ၏ Chat History ကို Admin အားလုံးနှင့် ကျောင်းသား မြင်နိုင်ရန် ဆွဲထုတ်မည်
    //     $messages = Message::where(function($q) use ($id) {
    //         $q->where('sender_id', $id)->orWhere('receiver_id', $id);
    //     })->orderBy('created_at', 'asc')->get();

    //     $view = $this->checkIsAdmin($currentUser) ? 'admin.contact.index' : 'user.contact.index';
    //     return view($view, compact('messages', 'id', 'receiver'));
    // }
    // Chat စာမျက်နှာ ပြသရန်
    public function index(Request $request, $receiverId = null) {
        $currentUser = auth()->user();

        $defaultReceiverId = 1;
        if ($this->checkIsAdmin($currentUser)) {
            $firstUser = User::where('role', 'user')->first();
            if ($firstUser) {
                $defaultReceiverId = $firstUser->id;
            }
        } else {
            // User ဘက်အတွက် Default Receiver က ပထမဆုံးတွေ့ရမယ့် Admin (သို့မဟုတ် Superadmin) ဖြစ်ရပါမည်
            $adminUser = User::whereIn('role', ['admin', 'superadmin'])->first();
            $defaultReceiverId = $adminUser ? $adminUser->id : 1;
        }

        $id = $receiverId ?? session('receiver_id', $defaultReceiverId);
        
        // တကယ်လို့ User ဘက်ကနေဝင်ပြီး ကိုယ့် ID ကိုပဲ Receiver အဖြစ် ဝင်လာရင် Admin ရց ID ကို အစားထိုးပေးမည်
        if (!$this->checkIsAdmin($currentUser) && $id == $currentUser->id) {
            $adminUser = User::whereIn('role', ['admin', 'superadmin'])->first();
            $id = $adminUser ? $adminUser->id : 1;
        }

        session(['receiver_id' => $id]);

        $receiver = User::findOrFail($id);

        if (!$this->checkIsAdmin($currentUser) && $receiver->id !== $currentUser->id) {
            // ဤနေရာတွင် User က Admin နဲ့ပြောနေတာဖြစ်လို့ လက်ခံနိုင်ပါသည်
        }

        // မဖတ်ရသေးသော မက်ဆေ့ချ်များကို ဖတ်ပြီးသားအဖြစ် ပြောင်းလဲခြင်း
        if ($this->checkIsAdmin($currentUser)) {
            Message::where('sender_id', $id)
                   ->whereIn('receiver_id', User::whereIn('role', ['admin', 'superadmin'])->pluck('id'))
                   ->where('is_read', 0)
                   ->update(['is_read' => 1]);
        } else {
            Message::where('sender_id', '!=', $currentUser->id)
                   ->where('receiver_id', $currentUser->id)
                   ->where('is_read', 0)
                   ->update(['is_read' => 1]);
        }

        // ကျောင်းသားတစ်ဦးချင်းစီ၏ Chat History ကို ဆွဲထုတ်မည်
        $messages = Message::where(function($q) use ($currentUser, $id) {
            if ($this->checkIsAdmin($currentUser)) {
                $q->where('sender_id', $id)->orWhere('receiver_id', $id);
            } else {
                // User ဘက်အတွက် ကိုယ်ပို့ထားတာနဲ့ Admin ဘက်ကနေ ပြန်ပို့ထားတာတွေအားလုံးကို ဆွဲထုတ်ရန်
                $q->where(function($sub) use ($currentUser, $id) {
                    $sub->where('sender_id', $currentUser->id)->where('receiver_id', $id);
                })->orWhere(function($sub) use ($currentUser, $id) {
                    $sub->where('sender_id', $id)->where('receiver_id', $currentUser->id);
                });
            }
        })->orderBy('created_at', 'asc')->get();

        $view = $this->checkIsAdmin($currentUser) ? 'admin.contact.index' : 'user.contact.index';
        return view($view, compact('messages', 'id', 'receiver'));
    }

    // မက်ဆေ့ချ်အသစ် သိမ်းဆည်းခြင်း
    public function store(Request $request) {
        $sender = auth()->user();
        $receiverId = $request->receiver_id;

        // အကယ်၍ ပို့သူက ကျောင်းသား (User) ဖြစ်နေလျှင် 
        if (!$this->checkIsAdmin($sender)) {
            // ကျောင်းသားပို့သောစာကို Admin အားလုံး မြင်နိုင်ရန် ပထမဆုံး Admin (သို့မဟုတ် Default Admin ID) သို့ ပို့မည် 
            // သို့သော် Chat query တွင် sender/receiver နှစ်ဖက်စလုံးကို ခြုံငုံဖမ်းယူထားပြီးဖြစ်므로 အဆင်ပြေပါသည်
            $admin = User::whereIn('role', ['admin', 'superadmin'])->first();
            $receiverId = $admin ? $admin->id : 1;
        } else {
            // Admin/Superadmin ဘက်က ပြန်တဲ့အခါ Form ထဲပါလာတဲ့ ကျောင်းသား ID (`receiver_id`) ကို ယူမည်
            $receiverId = $request->receiver_id;
        }

        if (!$receiverId) {
            return back()->with('error', 'Receiver ID မတွေ့ရှိပါ။');
        }

        $receiver = User::findOrFail($receiverId);

        if (!$this->checkIsAdmin($sender) && $sender->role === 'user' && $receiver->role === 'user') {
            return back()->with('error', 'ကျောင်းသားအချင်းချင်း စာပို့ခွင့်မရှိပါ။');
        }

        Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'message' => $request->message
        ]);

        return back();
    }
}