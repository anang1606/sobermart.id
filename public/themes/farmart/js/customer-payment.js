var paymentId;

$(document).on("click", ".upload-payment", function () {
    $("#preview-image-upload").html(inputPreviewImage());
    $("#modalPayment").modal("show");
    $("#bank_holder").text($(this).data("holder"));
    paymentId = $(this).data("uuid");
    $("#nomor_rekening").val("");
    $("#nama_pengirim").val("");
});

$(document).on("click", ".eg8apji0", function () {
    const fileInput = document.getElementById("input-file");
    fileInput.click();
});

$(document).on("click", ".eg8apji0-cancel", function () {
    $("#preview-image-upload").html(inputPreviewImage());
});

$(document).on("click", ".eg8apji0-upload", function () {
    const fileInput = $("#input-file")[0];
    const file = fileInput.files[0];
    const url = atob($("#input-file").data("url"));

    const formData = new FormData();
    formData.append("file", file);
    formData.append("payment_id", paymentId);
    formData.append("nomor_rekening", $("#nomor_rekening").val());
    formData.append("nama_pengirim", $("#nama_pengirim").val());

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (!response.error) {
                Toastify({
                    text: "Berhasil mengunggah bukti pembayaran",
                    gravity: "top",
                    position: "center",
                    style: {
                        background: "#0f3443",
                    },
                }).showToast();
                $("#modalPayment").modal("hide");
            }
        },
        error: function (xhr, status, error) {
            // Handle error response from the server
            // console.log("Upload error:", error);
            Toastify({
                text: error.data,
                gravity: "top",
                position: "center",
                style: {
                    background: "red",
                },
            }).showToast();
        },
    });
});

$(document).on("change", "#input-file", function () {
    var file = this.files[0];
    var reader = new FileReader();

    reader.onload = function (e) {
        var imageUrl = e.target.result;
        $("#preview-image-upload").html(previewImage(imageUrl));
    };

    if (file) {
        reader.readAsDataURL(file);
    }
});

const previewImage = (imageUrl) => {
    return `
        <div class="css-cm4bmz">
            <div class="css-1rphv4a">
                <img src="${imageUrl}" alt="" class="css-1i44bgl" />
            </div>
        </div>
        <div class="css-xi606m">
            <p class="css-3sfk19">Apakah kamu yakin ingin mengunggah gambar ini sebagai bukti pembayaran?</p>
            <button class="css-1icpry0-unf-btn eg8apji0-upload">
                <span>Unggar Gambar Ini</span>
            </button>
            <button class="css-68956l-unf-btn eg8apji0-cancel">
                <span>Batalkan</span>
            </button>
        </div>
    `;
};

const inputPreviewImage = () => {
    const placeHolderImg = $("#preview-image-upload").data("placeholderimg");
    return `
        <div class="css-cm4bmz">
                                <div class="css-1bdw6ln">
                                    <img src="${placeHolderImg}"
                                        alt="" />
                                    <p class="css-3sfk19">Format gambar: .JPG .JPEG, .PNG, max 10 MB</p>
                                    <button class="css-1icpry0-unf-btn eg8apji0">
                                        <span>Pilih Gambar</span>
                                    </button>
                                </div>
                            </div>
    `;
};
