<script> 
    document.querySelector("body").classList.toggle("active");
    var hamburger = document.querySelector(".hamburger");
    hamburger.addEventListener("click", function() {
        document.querySelector("body").classList.toggle("active");
    })
</script>