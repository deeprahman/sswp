<?php

use PHPUnit\Framework\TestCase;

class HTAccessFormTest extends TestCase
{
    private $sswp;
    private $sd;

    protected function setUp(): void
    {
        $this->sd = Sswp_Server_Directives_Factory::create_server_directives();

    }

    public function testHandleHtaccessPostReq()
    {
        $testData = [
            [
                "name" => "protect-debug-log",
                "value" => "off"
            ],
            [
                "name" => "allowed_files",
                "value" => ["jpeg", "gif"]
            ],
            [
                "name" => "protect-xml-rpc",
                "value" => "on"
            ],
            [
                "name" => "protect-rest-endpoint",
                "value" => "off"
            ]
        ];

        // Mock global functions
        global $allowed_functions;
        $allowed_functions = [
            "protect-debug-log" => "sswp_protect_debug_log",
            "allowed_files" => "sswp_protect_update_directory", // NOTE: make the file name consistent
            "protect-rest-endpoint" => "sswp_protect_rest_endpoint",
        ];


        // Run the function
        sswp_handle_htaccess_post_req($testData);
    }

    public function testsswpSaveHtaccessOption()
    {
        $testData = [
            ["name" => "test-option", "value" => "test-value"]
        ];

        // Mock get_options and update_option functions
        global $sswp;
        $sswp = $this->sswp;

        $this->getFunctionMock(__NAMESPACE__, 'get_options')
             ->expects($this->exactly(2))
             ->willReturnOnConsecutiveCalls(
                 ['_sswp_settings' => ['htaccess' => []]],
                 ['_sswp_settings' => ['htaccess' => ['ht_form' => $testData]]]
             );

        $this->getFunctionMock(__NAMESPACE__, 'update_option')
             ->expects($this->once())
             ->with('_sswp_settings', ['htaccess' => ['ht_form' => $testData]]);

        $result = sswp_save_htaccess_option($testData);

        $this->assertEquals(['ht_form' => $testData], $result);
    }

    public function testProtectDebugLog()
    {
        $this->sd->expects($this->once())->method('sswp_protect_debug_log');
        sswp_protect_debug_log("off", $this->sd);

        $this->sd->expects($this->once())->method('unprotect_debug_log');
        sswp_protect_debug_log("on", $this->sd);
    }

    public function testProtectUpdateDirectory()
    {
        $testFiles = ["jpeg", "gif"];

        // Mock the allowed_files function
        $this->getFunctionMock(__NAMESPACE__, 'allowed_files')
             ->expects($this->once())
             ->willReturn($testFiles);

        $this->sd->expects($this->once())->method('allow_file_access')->with($testFiles);
        sswp_protect_update_directory($testFiles, $this->sd);

        // Test with empty files
        $this->getFunctionMock(__NAMESPACE__, 'allowed_files')
             ->expects($this->once())
             ->willReturn([]);

        $this->sd->expects($this->once())->method('disallow_file_access');
        sswp_protect_update_directory([], $this->sd);
    }

    public function testProtectRestEndpoint()
    {
        $this->sd->expects($this->once())->method('protect_user_rest_apt');
        sswp_protect_rest_endpoint("off", $this->sd);

        $this->sd->expects($this->once())->method('unprotect_user_rest_apt');
        sswp_protect_rest_endpoint("on", $this->sd);
    }

    public function testAllowedFiles()
    {
        global $sswp_htaccess_from_settings;
        $sswp_htaccess_from_settings = [
            "file_types" => [
                "file_types" => ["jpeg", "gif", "png"]
            ]
        ];

        $testInput = [
            "value" => ["jpeg", "gif", "txt"]
        ];

        $result = allowed_files($testInput);
        $this->assertEquals(["jpeg", "gif"], $result);

        // Test with empty input
        $emptyInput = ["value" => []];
        $emptyResult = allowed_files($emptyInput);
        $this->assertEquals([], $emptyResult);
    }
}
