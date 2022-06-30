<center class="generator-box">
    <form action="/#generator" method="post">
        <h2 class="form-title">Hash Generator</h2>
            <div class="result">            
				<?php //Print hash result
					
					//Check if for submited
					if(isset($_POST["submitHashGen"])) {

						//Get plain text and escaped
						$text = $mysqlUtils->escapeString($_POST["text"], true, true);

						//Get hash type and escaped
						$hashType = $mysqlUtils->escapeString($_POST["hashType"], true, true);

						//Check if plaintext is not empty
						if (empty($text)) {
							echo '<div type="text" class="result__viewbox">Hash text is empty!</div>';
						} else {

							//Hash text to hash by type
							if ($hashType == "BlowFish") {
								$hash = $hashUtils->genBlowFish($text);
							
							} elseif ($hashType == "SHA1") {
								$hash = $hashUtils->genSHA1($text);

							} elseif ($hashType == "SHA224") {
								$hash = $hashUtils->customhash($text, "sha224");

							} elseif ($hashType == "SHA256") {
								$hash = $hashUtils->genSHA256($text);

							} elseif ($hashType == "SHA384") {
								$hash = $hashUtils->customhash($text, "sha384");

							} elseif ($hashType == "SHA512") {
								$hash = $hashUtils->customhash($text, "sha512");

							} elseif ($hashType == "SHA3-512") {
								$hash = $hashUtils->customhash($text, "sha3-512");

							} elseif ($hashType == "RIPEMD128") {
								$hash = $hashUtils->customhash($text, "ripemd128");

							} elseif ($hashType == "RIPEMD160") {
								$hash = $hashUtils->customhash($text, "ripemd160");

							} elseif ($hashType == "RIPEMD256") {
								$hash = $hashUtils->customhash($text, "ripemd256");

							} elseif ($hashType == "RIPEMD320") {
								$hash = $hashUtils->customhash($text, "ripemd320");

							} elseif ($hashType == "MD2") {
								$hash = $hashUtils->customhash($text, "md2");

							} elseif ($hashType == "MD4") {
								$hash = $hashUtils->customhash($text, "md4");

							} elseif ($hashType == "MD5") {
								$hash = $hashUtils->hashMD5($text);

							} elseif ($hashType == "CRC32") {
								$hash = $hashUtils->customhash($text, "crc32");

							} elseif ($hashType == "CRC32B") {
								$hash = $hashUtils->customhash($text, "crc32b");

							} elseif ($hashType == "Whirlpool") {
								$hash = $hashUtils->customhash($text, "whirlpool");

							} elseif ($hashType == "Snefru") {
								$hash = $hashUtils->customhash($text, "snefru");

							} elseif ($hashType == "Gost") {
								$hash = $hashUtils->customhash($text, "gost");

							} elseif ($hashType == "Adler32") {
								$hash = $hashUtils->customhash($text, "adler32");

							} elseif ($hashType == "Snefru256") {
								$hash = $hashUtils->customhash($text, "snefru256");

							} elseif ($hashType == "Fnv132") {
								$hash = $hashUtils->customhash($text, "fnv132");

							} elseif ($hashType == "Joaat") {
								$hash = $hashUtils->customhash($text, "joaat");

							//Print error if hash not found
							} else {
								echo '<div type="text" class="result__viewbox">Unknown hash type</div>';
							}


							//Print final hash and log to mysql
							if (!empty($hash)) {
                                echo '<input type="text" name="text" class="form-control text-input" aria-label="Small" aria-describedby="inputGroup-sizing-sm" value='.$hash.'>';

								//Insert result to mysql
								$mysqlUtils->insertQuery("INSERT INTO `hash_gen`(`text`, `hashType`, `hash`) VALUES ( '$text', '$hashType', '$hash')");   
							}
						}
					} else {
						echo '<div type="text" class="result__viewbox">Waiting for confirmation ...</div>';
					}
				?>
            </div>
            <div class="input-group input-group-sm mb-3">
                <input class="text-input" type="text" name="text" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Text">
                <div class="input-group mb-3">
                    <select name="hashType" class="custom-select">
                        <option selected>Select algorithm</option>
                        <option value="BlowFish">BlowFish</option>
                        <option value="SHA1">SHA1</option>
                        <option value="SHA224">SHA224</option>
                        <option value="SHA256">SHA256</option>
                        <option value="SHA384">SHA384</option>
                        <option value="SHA512">SHA512</option>
                        <option value="SHA3-512">SHA3-512</option>
                        <option value="RIPEMD128">RIPEMD128</option>
                        <option value="RIPEMD160">RIPEMD160</option>
                        <option value="RIPEMD256">RIPEMD256</option>
                        <option value="RIPEMD320">RIPEMD320</option>
                        <option value="MD2">MD2</option>
                        <option value="MD4">MD4</option>
                        <option value="MD5">MD5</option>
                        <option value="CRC32">CRC32</option>
                        <option value="CRC32B">CRC32B</option>
                        <option value="Whirlpool">Whirlpool</option>	
                        <option value="Snefru">Snefru</option>
                        <option value="Gost">Gost</option>
                        <option value="Adler32">Adler32</option>
                        <option value="Snefru256">Snefru256</option>
                        <option value="Fnv132">Fnv132</option>
                        <option value="Joaat">Joaat</option>
                    </select>
                </div>
            </div>
            <button class="input-button btn" name="submitHashGen">Generate Hash</button>
        </div>
    </form>
</center>