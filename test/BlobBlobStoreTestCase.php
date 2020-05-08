<?php
declare(strict_types=1);

namespace Plaisio\BlobStore\Test;

use PHPUnit\Framework\TestCase;
use Plaisio\Kernel\Nub;

/**
 * Parent class for test cases for BlobBlobStore.
 */
class BlobBlobStoreTestCase extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The kernel.
   *
   * @var Nub
   */
  protected $kernel;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the number of rows in table ABC_BLOB.
   */
  protected function getBlobCount(): int
  {
    return Nub::$nub->DL->executeSingleton1('select count(*) from `ABC_BLOB`');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the number of rows in table ABC_BLOB_DATA.
   */
  protected function getBlobDataCount(): int
  {
    return Nub::$nub->DL->executeSingleton1('select count(*) from `ABC_BLOB_DATA`');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to the MySQL server and cleans the BLOB tables.
   */
  protected function setUp(): void
  {
    $this->kernel = new TestKernelSys();

    Nub::$nub->DL->executeNone('set foreign_key_checks = 0');
    Nub::$nub->DL->executeNone('truncate table `ABC_BLOB`');
    Nub::$nub->DL->executeNone('truncate table `ABC_BLOB_DATA`');
    Nub::$nub->DL->executeNone('set foreign_key_checks = 1');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Disconnects from the MySQL server.
   */
  protected function tearDown(): void
  {
    Nub::$nub->DL->disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
