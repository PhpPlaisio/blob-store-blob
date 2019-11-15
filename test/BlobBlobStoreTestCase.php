<?php
declare(strict_types=1);

namespace Plaisio\BlobStore\Test;

use PHPUnit\Framework\TestCase;
use Plaisio\C;
use Plaisio\CompanyResolver\UniCompanyResolver;
use Plaisio\Kernel\Nub;

/**
 * Parent class for test cases for BlobBlobStore.
 */
class BlobBlobStoreTestCase extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the number of rows in table ABC_BLOB.
   */
  protected function getBlobCount(): int
  {
    return Nub::$DL->executeSingleton1('select count(*) from `ABC_BLOB`');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the number of rows in table ABC_BLOB_DATA.
   */
  protected function getBlobDataCount(): int
  {
    return Nub::$DL->executeSingleton1('select count(*) from `ABC_BLOB_DATA`');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to the MySQL server and cleans the BLOB tables.
   */
  protected function setUp(): void
  {
    Nub::$DL              = new DataLayer();
    Nub::$companyResolver = new UniCompanyResolver(C::CMP_ID_SYS);

    Nub::$DL->connect('localhost', 'test', 'test', 'test');

    Nub::$DL->executeNone('set foreign_key_checks = 0');
    Nub::$DL->executeNone('truncate table `ABC_BLOB`');
    Nub::$DL->executeNone('truncate table `ABC_BLOB_DATA`');
    Nub::$DL->executeNone('set foreign_key_checks = 1');
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Disconnects from the MySQL server.
   */
  protected function tearDown(): void
  {
    Nub::$DL->disconnect();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
