   <div class="page-content header-clear-medium">
        <div class="card card-style">
            <div class="content">
                <p class="mb-0 text-center font-600 color-highlight">Account Verification</p>
                <h1 class="text-center">NIN & BVN VERIFICATION</h1>
                <hr/>

                <div class="d-flex">
                    <h5 style="background:<?php echo $sitecolor; ?>; color:#ffffff; padding:9px;  margin-right:5px;">Note: </h5>
                    <marquee direction="left" scrollamount="5" style="background:#f2f2f2; padding:3px; border-radius:5rem;">
                        <h5 class="py-2">
                            Dear customer, in line with CBN circular in accordance with virtual account, you can either link your NIN or BVN. Rest assured, this is an easy process, and note, we do not store the details on our server.
                        </h5>
                    </marquee>
                </div>
                <hr/>
                <p class="mb-0 text-center font-600 color-highlight">Kindly Submit Either Your NIN or BVN</p>
                
                <br>

                <form id="verificationForm">
                    <fieldset>
                        <!-- NIN Input -->
                        <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="nin" class="color-theme opacity-80 font-700 font-12">NIN number</label>
                            <input type="text" name="nin" placeholder="Enter your NIN" class="round-small" id="nin" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" />
                        </div>
                        
                        <br>
                       
                        <p class="mb-0 text-center font-600 color-highlight">Or</p>
                        
                        <br>

                        <!-- BVN Input -->
                        <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="bvn" class="color-theme opacity-80 font-700 font-12">BVN number</label>
                            <input type="text" name="bvn" placeholder="Enter your BVN" class="round-small" id="bvn" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" />
                        </div>

                        <!-- Verify Button -->
                        <div class="form-button">
                            <button type="button" onclick="verifyNINandBVN()" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Verify
                            </button>
                        </div>
                    </fieldset>
                </form>

                <br>
                <!-- Display Verification Status -->
                <div id="verificationStatus" class="text-center mb-3"></div>
            </div>
        </div>
    </div>

    <script>
        function verifyNINandBVN() {
            var nin = document.getElementById("nin").value.trim();
            var bvn = document.getElementById("bvn").value.trim();

            if (nin === "" && bvn === "") {
                displayVerificationStatus("Please enter either NIN or BVN for verification.");
                return;
            }

            var monnifyEndpointNIN = 'https://api.monnify.com/api/v1/vas/nin-details';
            var monnifyEndpointBVN = 'https://api.monnify.com/api/v1/vas/bvn-details-match';

            if (nin !== "") {
                verifyNIN(monnifyEndpointNIN, nin);
            } else {
                var name = "User Full Name"; // Replace with actual user's full name
                var dob = "User Date of Birth"; // Replace with actual user's date of birth (in the format "YYYY-MM-DD")
                var mobileNo = "User Mobile Number"; // Replace with actual user's mobile number
                verifyBVN(monnifyEndpointBVN, bvn, name, dob, mobileNo);
            }
        }

        function verifyNIN(endpoint, nin) {
            var requestData = { nin: nin };
            makeMonnifyRequest(endpoint, requestData);
        }

        function verifyBVN(endpoint, bvn, name, dob, mobileNo) {
            var requestData = { bvn: bvn, name: name, dateOfBirth: dob, mobileNo: mobileNo };
            makeMonnifyRequest(endpoint, requestData);
        }

        function makeMonnifyRequest(endpoint, requestData) {
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Add any additional headers if needed
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => handleMonnifyResponse(data))
            .catch(error => {
                console.error('Error in Monnify API request:', error);
                displayVerificationStatus("Your information has been submitted for review, Virtual account will be generated once confirmed");
            });
        }

        function handleMonnifyResponse(response) {
            var verificationStatusElement = document.getElementById("verificationStatus");
            var message = "";

            if (response.requestSuccessful) {
                if (response.responseCode === "0" || response.responseCode === "00") {
                    message = "Verification successful!";
                } else {
                    message = "Verification failed: " + response.responseMessage;
                }
            } else {
                message = "Verification failed. Please try again.";
            }

            displayVerificationStatus(message);
        }

        function displayVerificationStatus(message) {
            var verificationStatusElement = document.getElementById("verificationStatus");
            verificationStatusElement.innerHTML = message;
        }
    </script>