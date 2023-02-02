<form class="imageUploadForm ac-form" action="?admin=accountSettings" method="post" enctype="multipart/form-data">
    <h2 class="loginFormTitle">Change profile image</h2>
        <div class="custom-file">
            <input type="file" class="custom-file-input bg-dark bg-image-input" name="fileToUpload" id="file-upload">
            <label class="custom-file-label bg-dark bg-image-input">Choose file</label>
        </div>
  <div class="rightPosition"><input class="inputButton" type="submit" value="Upload Image" name="submitUploadImage"></div>
</form>

<script type="application/javascript">
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });
</script>