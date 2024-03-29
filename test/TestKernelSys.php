<?php
declare(strict_types=1);

namespace Plaisio\BlobStore\Test;

use Plaisio\BlobStore\BlobBlobStore;
use Plaisio\C;
use Plaisio\CompanyResolver\CompanyResolver;
use Plaisio\CompanyResolver\UniCompanyResolver;
use Plaisio\PlaisioKernel;
use SetBased\Stratum\MySql\MySqlDefaultConnector;

/**
 * Kernel for testing purposes.
 */
class TestKernelSys extends PlaisioKernel
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the BLOB store object.
   *
   * @return BlobBlobStore
   */
  public function getBlob(): BlobBlobStore
  {
    return new BlobBlobStore($this);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the helper object for deriving the company.
   *
   * @return CompanyResolver
   */
  public function getCompany(): CompanyResolver
  {
    return new UniCompanyResolver(C::CMP_ID_SYS);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the data layer generated by PhpStratum.
   *
   * @return Object
   */
  protected function getDL(): object
  {
    $connector = new MySqlDefaultConnector('127.0.0.1', 'test', 'test', 'test');
    $dl        = new TestDataLayer($connector);
    $dl->connect();

    return $dl;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
