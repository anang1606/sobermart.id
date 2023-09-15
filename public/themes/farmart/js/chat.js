const iframeChat = document.getElementById('frame-chat')
const ROOT_API = 'https://apichat.sobermart.id'

$(document).on("click", ".css-ehm296-unf-btn", function () {
    if (getWindowWidth() <= 1024) {
        iframeChat.src = iframeChat.src.replace('nref=sm','nref=md')
    }
    document.body.style.overflow = 'hidden'
    setTimeout(() => {
        $(this).removeClass("css-ehm296-unf-btn");
        $(this).addClass("css-tykndw-unf-btn");
        $(".css-1lya59d").addClass("css-257zdh");
        $(".css-1lya59d").removeClass("css-1lya59d");
    }, 250);
});
$(document).on("click", ".chat-button", function () {
    if (getWindowWidth() <= 1024) {
        iframeChat.src = iframeChat.src.replace('nref=sm','nref=md')
    }

    document.body.style.overflow = 'hidden'
    setTimeout(() => {
        $(".css-ehm296-unf-btn").addClass("css-tykndw-unf-btn");
        $(".css-ehm296-unf-btn").removeClass("css-ehm296-unf-btn");
        $(".css-1lya59d").addClass("css-257zdh");
        $(".css-1lya59d").removeClass("css-1lya59d");
    }, 250);
});

const fetchQuery = (search) => {
    var url = new URL(iframeChat.src);
    var query = url.search;

    const urlParams = new URLSearchParams(query);
    return urlParams.get(search);
}

const getContactList = async (key) => {
    try {
        const response = await axios({
            method: 'GET',
            url: `${ROOT_API}/api/getContacts/${key}/${fetchQuery('type')}/${fetchQuery('store')}`,
        })
        return response.data;
    } catch (error) {
        console.error(error);
    }
}

const loadContact = () => {
    getContactList(fetchQuery('key')).then((response) => {
        const result = response

        const chatImgWrapper = document.getElementById('chatImgWrapper')
        const unreadMessages = result.data.contacts.filter((item) => item.unread_seen > 0);
        if(unreadMessages.length > 0){
            chatImgWrapper.innerHTML = `
                <i class="icon icon-bubble" style="font-size: 21px;margin-right: 7px;"></i>
                <span class="chatUnreadsCounter">${unreadMessages.length}</span>
            `
        }else{
            chatImgWrapper.innerHTML = `
                <i class="icon icon-bubble" style="font-size: 21px;margin-right: 7px;"></i>
            `
        }

        // console.log(unreadMessages)
    }).catch((error) => {
        console.error(error)
    })
}

window.addEventListener("message", function (event) {
    // if (event.origin !== "http://localhost:5173") {
    if (event.origin !== "https://chat.sobermart.id") {
        return; // Melakukan validasi untuk menghindari serangan lintas-dokumen (cross-site scripting)
    }
    document.body.style.overflow = ''
    iframeChat.src = iframeChat.src;
    var message = event.data;
    if (message === "closeMessage") {
        $(".css-tykndw-unf-btn").addClass("css-ehm296-unf-btn");
        $(".css-tykndw-unf-btn").removeClass("css-tykndw-unf-btn");
        $(".css-257zdh").addClass("css-1lya59d");
        $(".css-257zdh").removeClass("css-257zdh");
    }

    // if(message === 'get_notif'){

    // }
    // return;
    // console.log('Pesan yang diterima dari iframe: ' + message);
});

function getWindowWidth() {
  var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
  return width;
}

$(document).ready(()=> {
    setTimeout(() => {
        loadContact()
        const pusher = new Pusher('5fe41b6df7e4db75238c', {
            cluster: 'ap1',
            authEndpoint: `${ROOT_API}/api/pusher/auth`,
            auth: {
                transport: "ajax",
                params: {
                    'user_id': fetchQuery('key'),
                    'type': fetchQuery('type'),
                },
            },
        });

        const privateChannel = pusher.subscribe("private-channel");
        privateChannel.bind("chating", function (data) {
            loadContact()
        });
    },150)
})
