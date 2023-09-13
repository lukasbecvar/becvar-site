<form class="image-upload-form dark-table" action="?admin=accountSettings" method="post" enctype="multipart/form-data">
    <h2 class="login-form-title">Change profile image</h2>
        <div class="custom-file">
            <input type="file" class="custom-file-input bg-dark bg-image-input" name="fileToUpload" id="file-upload">
            <label class="custom-file-label bg-dark bg-image-input">Choose file</label>
        </div>
  <div class="right-position"><input class="input-button" type="submit" value="Upload Image" name="submitUploadImage"></div>
</form>

<script type="application/javascript">
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });
</script>