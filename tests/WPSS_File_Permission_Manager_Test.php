<?php

use PHPUnit\Framework\TestCase;

class WPSS_File_Permission_Manager_Test extends TestCase
{
    private $manager;
    private $wp_filesystem_mock;


    protected function setUp(): void
    {
        // Create a mock of the WordPress filesystem object
        $this->wp_filesystem_mock = $this->createMock(WP_Filesystem_Base::class);

        // Inject the mock into the manager
        $this->manager = new WPSS_File_Permission_Manager($this->wp_filesystem_mock);
    }

    public function testCheckPermissions()
    {
        $this->wp_filesystem_mock->method('exists')->willReturn(true);
        $this->wp_filesystem_mock->method('getchmod')->willReturn('644');
        $this->wp_filesystem_mock->method('is_writable')->willReturn(true);
        $this->wp_filesystem_mock->method('is_dir')->willReturn(false);

        $results = $this->manager->check_permissions();

        $this->assertIsArray($results);
        $this->assertArrayHasKey('wp-config.php', $results);
        $this->assertEquals(true, $results['wp-config.php']['exists']);
        $this->assertEquals('644', $results['wp-config.php']['permission']);
        $this->assertEquals(true, $results['wp-config.php']['writable']);
        $this->assertEquals('644', $results['wp-config.php']['recommended']);
    }

    public function testIsWithinWordPress()
    {
        $reflection = new ReflectionClass($this->manager);
        $method = $reflection->getMethod('is_within_wordpress');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->manager, ABSPATH . 'wp-config.php'));
        $this->assertFalse($method->invoke($this->manager, '/etc/passwd'));
    }

    public function testChangeFilePermission()
    {
        $this->wp_filesystem_mock->method('exists')->willReturn(true);
        $this->wp_filesystem_mock->expects($this->once())
                                 ->method('chmod')
                                 ->with($this->equalTo(ABSPATH . 'wp-config.php'), $this->equalTo(0644))
                                 ->willReturn(true);

        $result = $this->manager->change_file_permission(ABSPATH . 'wp-config.php', '644');
        $this->assertTrue($result);
    }

    public function testChangeToRecommendedPermissions()
    {
        $this->wp_filesystem_mock->method('exists')->willReturn(true);
        $this->wp_filesystem_mock->method('is_dir')->willReturn(true);
        $this->wp_filesystem_mock->method('dirlist')->willReturn([
            'file1.php' => ['type' => 'f'],
            'subdir' => ['type' => 'd'],
        ]);
        $this->wp_filesystem_mock->expects($this->exactly(3))
                                 ->method('chmod')
                                 ->willReturn(true);

        $result = $this->manager->change_to_recommended_permissions(ABSPATH . 'wp-content');
        $this->assertTrue($result);
    }

    public function testSetRecommendedPermission()
    {
        $this->assertTrue($this->manager->set_recommended_permission('directory', '755'));
        $this->assertTrue($this->manager->set_recommended_permission('file', '644'));
        $this->assertFalse($this->manager->set_recommended_permission('invalid', '644'));
        $this->assertFalse($this->manager->set_recommended_permission('file', '999'));
    }

    public function testGetCurrentPermission()
    {
        $this->wp_filesystem_mock->method('exists')->willReturn(true);
        $this->wp_filesystem_mock->method('getchmod')->willReturn('644');

        $permission = $this->manager->get_current_permission(ABSPATH . 'wp-config.php');
        $this->assertEquals('644', $permission);
    }

    public function testSetPermission()
    {
        $this->wp_filesystem_mock->method('exists')->willReturn(true);
        $this->wp_filesystem_mock->expects($this->once())
                                 ->method('chmod')
                                 ->with($this->equalTo(ABSPATH . 'wp-config.php'), $this->equalTo(0644))
                                 ->willReturn(true);

        $result = $this->manager->set_permission(ABSPATH . 'wp-config.php', '644');
        $this->assertTrue($result);
    }
}
