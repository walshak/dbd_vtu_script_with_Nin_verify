<?php

namespace Tests\Feature;

use Tests\TestCase;

class VTUFrameworkTest extends TestCase
{
    public function test_vtu_framework_js_file_exists()
    {
        $this->assertFileExists(public_path('js/vtu-framework.js'));
    }

    public function test_vtu_services_js_file_exists()
    {
        $this->assertFileExists(public_path('js/vtu-services.js'));
    }

    public function test_vtu_framework_contains_core_class()
    {
        $content = file_get_contents(public_path('js/vtu-framework.js'));

        $this->assertStringContainsString('class VTUFramework', $content);
        $this->assertStringContainsString('makeRequest', $content);
        $this->assertStringContainsString('showModal', $content);
        $this->assertStringContainsString('hideModal', $content);
        $this->assertStringContainsString('validateForm', $content);
        $this->assertStringContainsString('showToast', $content);
    }

    public function test_vtu_framework_has_form_validation_methods()
    {
        $content = file_get_contents(public_path('js/vtu-framework.js'));

        $this->assertStringContainsString('validatePhoneNumber', $content);
        $this->assertStringContainsString('validateTransactionPin', $content);
        $this->assertStringContainsString('validateField', $content);
        $this->assertStringContainsString('showFieldError', $content);
        $this->assertStringContainsString('hideFieldError', $content);
    }

    public function test_vtu_framework_has_modal_management()
    {
        $content = file_get_contents(public_path('js/vtu-framework.js'));

        $this->assertStringContainsString('showLoadingModal', $content);
        $this->assertStringContainsString('hideLoadingModal', $content);
        $this->assertStringContainsString('showSuccessModal', $content);
        $this->assertStringContainsString('showErrorModal', $content);
    }

    public function test_vtu_framework_has_ajax_handler()
    {
        $content = file_get_contents(public_path('js/vtu-framework.js'));

        $this->assertStringContainsString('async makeRequest', $content);
        $this->assertStringContainsString('$.ajax', $content);
        $this->assertStringContainsString('_token', $content);
    }

    public function test_vtu_framework_has_wallet_management()
    {
        $content = file_get_contents(public_path('js/vtu-framework.js'));

        $this->assertStringContainsString('loadWalletBalance', $content);
        $this->assertStringContainsString('updateWalletBalance', $content);
        $this->assertStringContainsString('formatCurrency', $content);
    }

    public function test_vtu_framework_has_progress_step_management()
    {
        $content = file_get_contents(public_path('js/vtu-framework.js'));

        $this->assertStringContainsString('updateProgressStep', $content);
        $this->assertStringContainsString('currentStep', $content);
    }

    public function test_vtu_framework_has_form_submission_handler()
    {
        $content = file_get_contents(public_path('js/vtu-framework.js'));

        $this->assertStringContainsString('setupFormSubmission', $content);
        $this->assertStringContainsString('addEventListener', $content);
    }

    public function test_vtu_framework_initializes_globally()
    {
        $content = file_get_contents(public_path('js/vtu-framework.js'));

        $this->assertStringContainsString('window.VTU', $content);
        $this->assertStringContainsString('new VTUFramework', $content);
    }

    public function test_vtu_services_extends_framework()
    {
        $content = file_get_contents(public_path('js/vtu-services.js'));

        $this->assertStringContainsString('class VTUServiceFramework extends VTUFramework', $content);
        $this->assertStringContainsString('initializeAirtimeService', $content);
        $this->assertStringContainsString('initializeDataService', $content);
        $this->assertStringContainsString('initializeCableService', $content);
        $this->assertStringContainsString('initializeElectricityService', $content);
    }
}
