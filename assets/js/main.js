$(document).ready(function() {
    $("#new").hide();
    $(".csvForm").submit(function(e) {
        e.preventDefault();
        if ($('#myTable').length>=1) {
            $('#myTable').dataTable().fnDestroy();
        }
        $("#ajaxContent").fadeOut(300)
        var formData = new FormData(), fileField = $("#csvFile"), inputFile = fileField[0].files[0];
        formData.append('upload', inputFile)
        swal({
          html: 'Your file is being uploaded please wait.',
          timer: 2000,
          onOpen: () => {
            swal.showLoading()
          }
        })
        setTimeout(function() {
            $.ajax({
                url: myUrl+"upload.php",
                type: "post",
                processData: false,
                contentType: false,
                data: formData,
                success: function(msg) {
                    if (msg.indexOf("<table") != -1) {
                        $("#ajaxContent").html(msg);
                        $("#ajaxContent").fadeIn(300);
                        swal({
                          type: 'success',
                          title: 'File Uploaded',
                          html: "Click on any column to order the elements as needed, if you want to look for specific values please use the search box :).",
                          timer: 15500
                        });
                        $(".csvForm").fadeOut();
                        $("#new").fadeIn();
                    } else {
                        swal({
                          type: 'error',
                          title: 'Atention',
                          html: msg,
                          timer: 18500
                        })
                    }
                },
                error: function(exr) {
                    console.log(exr);
                }
            });
        },2000);
        $(this).trigger("reset");
    });
    $("#new").click(function() {
        $(".csvForm").fadeIn();
        $(this).fadeOut();
    });    
});