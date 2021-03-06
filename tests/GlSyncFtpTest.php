<?php
/**
 * Test GlSyncFtp
 *
 * PHP version 5.4
 *
 * @category  GLICER
 * @package   GlHtml\Tests
 * @author    Emmanuel ROECKER
 * @author    Rym BOUCHAGOUR
 * @copyright 2015 GLICER
 * @license   MIT
 * @link      http://dev.glicer.com/
 *
 * Created : 22/05/15
 * File : GlSyncFtpTest.php
 *
 */
namespace GlSyncFtp\Tests;

use GlSyncFtp\GlSyncFtp;

/**
 * @covers        \GlSyncFtp\GlSyncFtp
 * @backupGlobals disabled
 */
class GlSyncFtpTest extends \PHPUnit_Framework_TestCase
{

    public function testFtpNew()
    {
        $ftp    = new GlSyncFtp(FTP_SERVER_HOST, FTP_SERVER_PORT, FTP_SERVER_USER, FTP_SERVER_PASSWORD);
        $nbr    = 0;
        $nbrnew = 0;
        $ftp->syncDirectory(
            __DIR__ . '/new',
                '/data',
                function ($op, $path) use (&$nbr, &$nbrnew) {
                    switch ($op) {
                        case GlSyncFtp::CREATE_DIR:
                            $this->assertEquals(0, $nbr);
                            $this->assertEquals("/data/dir1", $path);
                            break;
                        case GlSyncFtp::NEW_FILE:
                            switch ($nbrnew) {
                                case 0:
                                    $this->assertEquals("/data/dir1/test1.txt", $path);
                                    break;
                                case 1:
                                    $this->assertEquals("/data/test2.txt", $path);
                                    break;
                                default:
                                    $this->fail();
                            }
                            $nbrnew++;
                            break;
                        default:
                            $this->fail();
                    }
                    $nbr++;
                }
        );

        $files = [];
        $dirs  = [];
        $ftp->getAllFiles('/data', $files, $dirs);

        $filesname = array_keys($files);
        $dirsname  = array_keys($dirs);

        $this->assertCount(2, $filesname);
        $this->assertEquals("/dir1/test1.txt", $filesname[0]);
        $this->assertEquals("/test2.txt", $filesname[1]);
        $this->assertCount(1, $dirsname);
        $this->assertEquals("/dir1", $dirsname[0]);
    }

    public function testFtpUpdate()
    {
        $ftp    = new GlSyncFtp(FTP_SERVER_HOST, FTP_SERVER_PORT, FTP_SERVER_USER, FTP_SERVER_PASSWORD);
        $nbr    = 0;
        $nbrnew = 0;
        $ftp->syncDirectory(
            __DIR__ . '/update',
                '/data',
                function ($op, $path) use (&$nbr, &$nbrnew) {
                    switch ($op) {
                        case GlSyncFtp::DELETE_FILE:
                            $this->assertEquals("/data/dir1/test1.txt", $path);
                            break;
                        case GlSyncFtp::DELETE_DIR:
                            $this->assertEquals("/data/dir1", $path);
                            break;
                        case GlSyncFtp::CREATE_DIR:
                            $this->assertEquals("/data/dir2", $path);
                            break;
                        case GlSyncFtp::UPDATE_FILE:
                            $this->assertEquals("/data/test2.txt", $path);
                            break;
                        case GlSyncFtp::NEW_FILE:
                            switch ($nbrnew) {
                                case 0:
                                    $this->assertEquals("/data/dir2/test3.txt", $path);
                                    break;
                                case 1:
                                    $this->assertEquals("/data/test2.txt", $path);
                                    break;
                                default:
                                    $this->fail();
                            }
                            $nbrnew++;
                            break;
                        default:
                            $this->fail();
                    }
                    $nbr++;
                }
        );

        $files = [];
        $dirs  = [];
        $ftp->getAllFiles('/data', $files, $dirs);

        $filesname = array_keys($files);
        $dirsname  = array_keys($dirs);

        $this->assertCount(2, $filesname);
        $this->assertEquals("/dir2/test3.txt", $filesname[0]);
        $this->assertEquals("/test2.txt", $filesname[1]);
        $this->assertCount(1, $dirsname);
        $this->assertEquals("/dir2", $dirsname[0]);
    }

    public function testFtpDelete()
    {
        $ftp       = new GlSyncFtp(FTP_SERVER_HOST, FTP_SERVER_PORT, FTP_SERVER_USER, FTP_SERVER_PASSWORD);
        $nbr       = 0;
        $nbrdelete = 0;
        $ftp->syncDirectory(
            __DIR__ . '/delete',
                '/data',
                function ($op, $path) use (&$nbr, &$nbrdelete) {
                    switch ($op) {
                        case GlSyncFtp::DELETE_DIR:
                            $this->assertEquals("/data/dir2", $path);
                            break;
                        case GlSyncFtp::DELETE_FILE:
                            switch ($nbrdelete) {
                                case 0:
                                    $this->assertEquals("/data/dir2/test3.txt", $path);
                                    break;
                                case 1:
                                    $this->assertEquals("/data/test2.txt", $path);
                                    break;
                                default:
                                    $this->fail();
                            }
                            $nbrdelete++;
                            break;
                        default:
                            $this->fail();
                    }
                    $nbr++;
                }
        );

        $files = [];
        $dirs  = [];
        $ftp->getAllFiles('/data', $files, $dirs);

        $this->assertCount(0, $files);
        $this->assertCount(0, $dirs);
    }

    public function testFtpDirectories()
    {
        $list = [
            __DIR__ . '/new'    => '/data',
            __DIR__ . '/update' => '/data',
            __DIR__ . '/delete' => '/data'
        ];

        $ftp = new GlSyncFtp(FTP_SERVER_HOST, FTP_SERVER_PORT, FTP_SERVER_USER, FTP_SERVER_PASSWORD);
        $nbr = 0;
        $nbrnew = 0;
        $ftp->syncDirectories(
            $list,
                function ($src, $dst) {
                },
                function ($op, $path) use (&$nbr, &$nbrnew) {
                    switch ($op) {
                        case GlSyncFtp::CREATE_DIR:
                            switch ($nbr) {
                                case 0:
                                    $this->assertEquals("/data/dir1", $path);
                                    break;
                                default:
                            }
                            break;
                        case GlSyncFtp::NEW_FILE:
                            switch ($nbrnew) {
                                case 0:
                                    $this->assertEquals("/data/dir1/test1.txt", $path);
                                    break;
                                case 1:
                                    $this->assertEquals("/data/test2.txt", $path);
                                    break;
                                default:

                            }
                            $nbrnew++;
                            break;
                        default:
                    }
                    $nbr++;
                }
        );

        $files = [];
        $dirs  = [];
        $ftp->getAllFiles('/data', $files, $dirs);

        $this->assertCount(0, $files);
        $this->assertCount(0, $dirs);
    }
} 
