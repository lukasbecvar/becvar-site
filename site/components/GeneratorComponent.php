<section id="generator" class="generator">
    <div class="container">
        <div class="section-title">
            <h2>Generator</h2>
        </div>
        <?php 
            //include passowrd generator form
            include(__DIR__."/../elements/forms/PasswordGeneratorForm.php");

            //include hash generator form
            include(__DIR__."/../elements/forms/HashGeneratorForm.php");
        ?>
    </div>
</section>
