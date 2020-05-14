<?php
declare(strict_types=1);

namespace Plaisio\BlobStore\Test;

use PHPUnit\Framework\TestCase;
use Plaisio\PlaisioKernel;

/**
 * Parent class for test cases for BlobBlobStore.
 */
class BlobBlobStoreTestCase extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The kernel.
   *
   * @var PlaisioKernel
   */
  protected $kernel;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the number of rows in table ABC_BLOB.
   */
  protected function getBlobCount(): int
  {
    return $this->kernel->DL->executeSingleton1('select count(*) from `ABC_BLOB`');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the number of rows in table ABC_BLOB_DATA.
   */
  protected function getBlobDataCount(): int
  {
    return $this->kernel->DL->executeSingleton1('select count(*) from `ABC_BLOB_DATA`');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to the MySQL server and cleans the BLOB tables.
   */
  protected function setUp(): void
  {
    $this->kernel = new TestKernelSys();

    $this->kernel->DL->executeNone('set foreign_key_checks = 0');
    $this->kernel->DL->executeNone('truncate table `ABC_BLOB`');
    $this->kernel->DL->executeNone('truncate table `ABC_BLOB_DATA`');
    $this->kernel->DL->executeNone('set foreign_key_checks = 1');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Disconnects from the MySQL server.
   */
  protected function tearDown(): void
  {
    $this->kernel->DL->disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
