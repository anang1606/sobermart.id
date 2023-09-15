@extends(EcommerceHelper::viewPath('customers.master'))
@section('content')
    <div style="background-color: #f2f2f2">
        <div class="app">
            <div class="wrapper-chat">
                <div class="chat-area">
                    <div class="chat-area-main pt-3">
                        @if (count($messages) > 0)
                            @foreach ($messages as $message)
                                <div class="chat-msg @if ((int)$message->is_admin === 0) owner @endif">
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
                            <input type="text" placeholder="Type something here..." required name="message"
                                autocomplete="off" />
                            <button class="btn btn-info" type="submit">
                                Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
