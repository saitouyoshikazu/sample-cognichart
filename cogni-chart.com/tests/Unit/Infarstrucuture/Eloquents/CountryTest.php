<?php

namespace Tests\Unit\Eloquents;
use Tests\TestCase;
use App\Infrastructure\Eloquents\Country;

class CountryTest extends TestCase
{

    public function testGet()
    {
        $countryIdValue = 'ZZ';
        $res = Country::get($countryIdValue);
        $this->assertNull($res);

        $countryIdValue = 'US';
        $verify = new \StdClass();
        $verify->id = 'US';
        $verify->countryName = 'USA';
        $res = Country::get($countryIdValue);
        $this->assertEquals($res, $verify);
    }

    public function testList()
    {
        $countryIdValues = ['ZZ'];
        $res = Country::list($countryIdValues);
        $this->assertNull($res);

        $countryIdValues = ['US'];
        $res = Country::list($countryIdValues);
        $this->assertEquals(count($res), 1);
        $res = $res[0];
        $this->assertEquals($res->id, 'US');

        $countryIdValues = ['US', 'GB'];
        $res = Country::list($countryIdValues);
        $this->assertEquals(count($res), 2);
        foreach ($res AS $row) {
            $searchedIndex = array_search($row->id, $countryIdValues);
            $searched = true;
            if ($searchedIndex === false) {
                $searched = false;
            }
            $this->assertTrue($searched);
        }

        $countryIdValues = ['US', 'GB', 'ZZ'];
        $res = Country::list($countryIdValues);
        $this->assertEquals(count($res), 2);

        $countryIdValues = null;
        $res = Country::list($countryIdValues);
        $this->assertEquals(count($res), 130);
    }
}
