@extends('core/base::layouts.chat_master')
@section('content')
    <div class="app">
        <div class="header">
            <div class="logo">
                <img src="{{ RvMedia::getImageUrl(setting('admin_logo')) }}" alt="">
            </div>
            <div></div>
            <div class="user-settings">
                <a href="/admin">
                    Kembali ke dashboard
                </a>
            </div>
        </div>
        <div class="wrapper">
            <div class="conversation-area">
                @foreach ($getContacts as $getContact)
                    <a href="{{ route('marketplace.support-message.details', ['redux_state' => base64_encode(json_encode($getContact))]) }}"
                        class="msg">
                        <img class="msg-profile" src="{{ $getContact->customer->avatar_url }}" alt="" />
                        <div class="msg-detail">
                            <div class="msg-username">{{ $getContact->customer->name }}</div>
                            <div class="msg-content">
                                <span class="msg-message">{{ BaseHelper::clean($getContact->message) }}</span>
                                <span
                                    class="msg-date">{{ date_format(new DateTime($getContact->created_at), 'H:i') }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
                <div class="overlay"></div>
            </div>
            <div class="chat-area">
                <div class="chat-area-main pt-3">
                    @if (count($messages) > 0)
                        @foreach ($messages as $message)
                            <div class="chat-msg @if ($message->is_admin === 1) owner @endif">
                                <div class="chat-msg-content">
                                    <div class="chat-msg-text">
                                        {{ BaseHelper::clean($message->message) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="css-pugdqi">
                            <div class="css-18wagsg"><img class="css-1pnjk8x"
                                    src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="welcome">
                                <h1 class="css-ei6gqf">Mari memulai obrolan!</h1>
                                <p class="css-1vhocgc">Pilih pesan di samping untuk mulai chat dengan pembeli.</p>
                            </div>
                        </div>
                    @endif
                </div>
                <form action="" method="post">
                    @csrf
                    <div class="chat-area-footer">
                        <input type="text" placeholder="Type something here..." required name="message" autocomplete="off" />
                        <button class="btn btn-info" type="submit">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
