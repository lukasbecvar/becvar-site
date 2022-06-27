<div class="pageContentText">
    <strong class="pageSubTitle">My Skills</strong><br><br>
    <div class="bar-1">
        <div class="title">PHP [Advanced]</div>
        <div class="bar" data-width="85%">
        <div class="bar-inner"></div>
        <div class="bar-percent"><strong>85%</strong></div>
        </div>
    </div>
	<div class="bar-1">
			<div class="title">HTML/CSS [Advanced]</div>
			<div class="bar" data-width="80%">
			<div class="bar-inner"></div>
			<div class="bar-percent"><strong>80%</strong></div>
		</div>
	</div>
    <div class="bar-1">
        <div class="title">Linux [Advanced]</div>
        <div class="bar" data-width="75%">
        <div class="bar-inner"></div>
        <div class="bar-percent"><strong>75%</strong></div>
        </div>
    </div>
    <div class="bar-1">
        <div class="title">JAVA [Intermediate]</div>
        <div class="bar" data-width="55%">
        <div class="bar-inner"></div>
        <div class="bar-percent"><strong>55%</strong></div>
        </div>
    </div>
    <div class="bar-1">
        <div class="title">SQL/Databases [Intermediate]</div>
        <div class="bar" data-width="45%">
        <div class="bar-inner"></div>
        <div class="bar-percent"><strong>45%</strong></div>
        </div>
    </div>
    <div class="bar-1">
        <div class="title">JavaScript [Beginner]</div>
        <div class="bar" data-width="30%">
    	<div class="bar-inner"></div>
        <div class="bar-percent"><strong>30%</strong></div>
        </div>
    </div>
</div>
<script>
	$(".bar").each(function(){
		$(this).find(".bar-inner").animate({
			width: $(this).attr("data-width")
		},8000)
	});
</script>
