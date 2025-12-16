<?php
require_once 'includes/common_init.php';
require_once 'includes/data_loaders.php';

// Load bulk SMS data
$smsData = loadBulkSmsData($controller);
$sitecolor = isset($siteSettings['sitecolor']) ? $siteSettings['sitecolor'] : '#007bff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Bulk SMS - <?php echo $siteName; ?></title>
    
    <!-- Mobile App Styling -->
    <link rel="stylesheet" type="text/css" href="../assets/styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../assets/styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- PWA Configuration -->
    <link rel="manifest" href="../assets/app.json">
    <meta name="theme-color" content="<?php echo $sitecolor; ?>">
    <link rel="apple-touch-icon" href="../assets/images/logo.png">
    
    <style>
        .info-marquee {
            background: #f2f2f2;
            padding: 3px;
            border-radius: 5rem;
            overflow: hidden;
        }
        .info-header {
            background: <?php echo $sitecolor; ?>;
            color: #ffffff;
            padding: 9px;
            margin-right: 5px;
            border-radius: 4px;
        }
        .character-counter {
            font-size: 12px;
            color: #666;
            text-align: right;
            margin-top: 5px;
        }
        .phone-counter {
            font-size: 12px;
            color: #666;
            text-align: right;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header header-fixed header-logo-center">
        <a href="dashboard" class="header-title"><?php echo $siteName; ?></a>
        <a href="dashboard" class="header-icon header-icon-1"><i class="fas fa-arrow-left"></i></a>
        <a href="account" class="header-icon header-icon-4"><i class="fas fa-user"></i></a>
    </div>

    <!-- Main Content -->
    <div class="page-content header-clear-medium">
        <div class="card card-style">
            <div class="content">
                <p class="mb-0 text-center font-600 color-highlight">SMS For All Networks</p>
                <h1 class="text-center">Send Bulk SMS</h1>

                <form method="post" id="bulkSMSForm" action="send-bulk-sms">
                    <fieldset>
                        <hr/>
                        <div class="d-flex">
                            <h5 class="info-header">Info: </h5>
                            <div class="info-marquee flex-grow-1">
                                <marquee direction="left" scrollamount="5">
                                    <h5 class="py-2">
                                        Type or Paste up to 10,000 phone numbers here (080... or 23480...) separate with comma, NO SPACES!
                                    </h5>
                                </marquee>
                            </div>
                        </div>
                        <hr/>

                        <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="senderName" class="color-theme opacity-80 font-700 font-12">Sender Name</label>
                            <input type="text" id="senderName" name="senderName" placeholder="Must not exceed 12 characters" class="round-small" maxlength="12" required />
                            <div class="character-counter">
                                <span id="senderCounter">0</span>/12 characters
                            </div>
                        </div>
                        
                        <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="phoneNumbers" class="color-theme opacity-80 font-700 font-12">Phone Numbers</label>
                            <textarea id="phoneNumbers" name="phoneNumbers" placeholder="Enter phone numbers here separated by a comma" class="round-small" rows="6" required></textarea>
                            <div class="phone-counter">
                                <span id="phoneCounter">0</span> numbers detected
                            </div>
                        </div>

                        <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="message" class="color-theme opacity-80 font-700 font-12">Message</label>
                            <textarea id="message" name="message" placeholder="Enter your message here" class="round-small" rows="4" required></textarea>
                            <div class="character-counter">
                                <span id="messageCounter">0</span>/160 characters (1 SMS)
                            </div>
                        </div>
                        
                        <div class="input-style input-style-always-active has-borders validate-field mb-4">
                            <label for="amounttopay" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                            <input type="number" name="amounttopay" placeholder="Amount To Pay" value="" class="round-small" id="amounttopay" readonly required />
                            <input type="hidden" name="calculatedAmount" id="calculatedAmount" />
                        </div>

                        <div class="alert alert-info" style="font-size: 12px;">
                            <h6><i class="fas fa-info-circle"></i> SMS Pricing Information:</h6>
                            <ul class="mb-0">
                                <li>Each SMS costs ₦4.00 per recipient</li>
                                <li>Messages over 160 characters count as multiple SMS</li>
                                <li>Delivery reports are included</li>
                                <li>Messages are sent instantly</li>
                            </ul>
                        </div>

                        <input name="transref" type="hidden" value="<?php echo $transRef; ?>" />
                        <input name="transkey" id="transkey" type="hidden" />

                        <div class="form-button">
                            <button type="submit" id="btnsubmit" name="proceedBtn" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Send Bulk SMS
                            </button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer Menu -->
    <div class="footer-menu footer-menu-style-3">
        <a href="dashboard"><i class="fas fa-home"></i><span>Home</span></a>
        <a href="fund-wallet"><i class="fas fa-wallet"></i><span>Wallet</span></a>
        <a href="transactions"><i class="fas fa-history"></i><span>History</span></a>
        <a href="account"><i class="fas fa-user"></i><span>Account</span></a>
    </div>

    <!-- Scripts -->
    <script src="../assets/scripts/jquery.js"></script>
    <script src="../assets/scripts/bootstrap.min.js"></script>
    <script src="../assets/scripts/custom.js"></script>
    
    <script>
    $(document).ready(function() {
        // SMS rate per message
        const SMS_RATE = 4.00; // ₦4.00 per SMS
        
        // Update sender name counter
        $('#senderName').on('input', function() {
            const length = $(this).val().length;
            $('#senderCounter').text(length);
        });

        // Update phone numbers counter and calculate cost
        $('#phoneNumbers').on('input', function() {
            updateCalculations();
        });

        // Update message counter and calculate SMS count
        $('#message').on('input', function() {
            updateCalculations();
        });

        function updateCalculations() {
            const phoneNumberInput = $('#phoneNumbers').val().trim();
            const messageInput = $('#message').val();
            
            // Count phone numbers
            let phoneCount = 0;
            if (phoneNumberInput) {
                // Split by comma and filter out empty strings
                const phones = phoneNumberInput.split(',').filter(phone => phone.trim() !== '');
                phoneCount = phones.length;
            }
            $('#phoneCounter').text(phoneCount);

            // Calculate message length and SMS count
            const messageLength = messageInput.length;
            const smsCount = Math.ceil(messageLength / 160) || 1;
            
            // Update message counter
            $('#messageCounter').text(messageLength);
            $('#messageCounter').parent().find('.character-counter').html(
                `<span id="messageCounter">${messageLength}</span>/160 characters (${smsCount} SMS${smsCount > 1 ? ' each' : ''})`
            );

            // Calculate total cost
            const totalSms = phoneCount * smsCount;
            const totalAmount = totalSms * SMS_RATE;
            
            $('#calculatedAmount').val(totalAmount);
            $('#amounttopay').val(totalAmount.toFixed(2));
        }

        // Form submission
        $('#bulkSMSForm').on('submit', function(e) {
            e.preventDefault();
            
            const phoneCount = parseInt($('#phoneCounter').text());
            const messageLength = $('#message').val().length;
            const senderName = $('#senderName').val().trim();
            
            // Validation
            if (phoneCount === 0) {
                alert('Please enter at least one valid phone number.');
                return;
            }
            
            if (phoneCount > 10000) {
                alert('Maximum 10,000 phone numbers allowed per bulk SMS.');
                return;
            }
            
            if (messageLength === 0) {
                alert('Please enter a message.');
                return;
            }
            
            if (senderName.length === 0) {
                alert('Please enter a sender name.');
                return;
            }
            
            // Confirmation
            const smsCount = Math.ceil(messageLength / 160) || 1;
            const totalSms = phoneCount * smsCount;
            const totalAmount = totalSms * SMS_RATE;
            
            const confirmMsg = `Send ${totalSms} SMS to ${phoneCount} recipients?\n\nCost: ₦${totalAmount.toFixed(2)}\n\nClick OK to proceed.`;
            
            if (!confirm(confirmMsg)) {
                return;
            }
            
            // Submit form
            let formData = new FormData(this);
            
            $.ajax({
                url: '../api/bulksms/index.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#btnsubmit').html('<i class="fas fa-spinner fa-spin"></i> Sending...');
                    $('#btnsubmit').prop('disabled', true);
                },
                success: function(response) {
                    if(response.status === 'success') {
                        alert('Bulk SMS sent successfully!');
                        window.location.href = 'transaction-details?ref=' + response.reference;
                    } else {
                        alert(response.message || 'An error occurred while sending SMS');
                    }
                },
                error: function() {
                    alert('Network error. Please try again.');
                },
                complete: function() {
                    $('#btnsubmit').html('Send Bulk SMS');
                    $('#btnsubmit').prop('disabled', false);
                }
            });
        });
    });
    </script>
</body>
</html>