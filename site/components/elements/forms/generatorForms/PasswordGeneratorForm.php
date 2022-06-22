<center>
	<div class="contentBox">
		<h2 class="boxTitle">Password Generator</h2>
			<div class="boxContent">
				<div class="result">
					<div class="result__viewbox" id="result"></div>
				</div>
				<div class="length range__slider" data-min="4" data-max="32">
					<div class="length__title field-title" data-length='0'></div>
					<input id="slider" type="range" min="4" max="32" value="16" />
				</div>
				<div class="settings">
					<div class="setting">
						<input type="checkbox" id="uppercase" checked />
						<label for="uppercase">Include Uppercase</label>
					</div>
					<div class="setting">
						<input type="checkbox" id="lowercase" checked />
						<label for="lowercase">Include Lowercase</label>
					</div>
					<div class="setting">
						<input type="checkbox" id="number" checked />
					<label for="number">Include Numbers</label>
				</div>
				<div class="setting">
					<input type="checkbox" id="symbol" />
					<label for="symbol">Include Symbols</label>
				</div>
			</div>
		</div>
        <button class="inputButton bg-dark btn generateButton" id="generate">Generate Password</button>
    </div>
</center>
<script type="text/javascript" src="assets/js/passwordGen.js"></script>