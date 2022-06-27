<form class="imageUploadForm bg-dark" action="?page=admin&process=accountSettings" method="post" enctype="multipart/form-data">
    <h2 class="loginFormTitle">Change profile image</h2>
        <div class="custom-file">
            <input type="file" class="custom-file-input" name="fileToUpload" id="inputGroupFile01">
            <label class="custom-file-label bg-dark">Choose file</label>
        </div>
  <div class="rightPosition"><input class="inputButton bg-dark" type="submit" value="Upload Image" name="submitUploadImage"></div>
</form>

<script type="application/javascript">
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });
</script>