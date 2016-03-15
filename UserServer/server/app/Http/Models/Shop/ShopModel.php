<?php
/**
* 收货地址相关业务逻辑操作---模块化2.0
*
* @author liangfeng@shinc.net
* @version v1.0
* @copyright shinc
*/

namespace App\Http\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class ShopModel extends Model
{
	protected $table;


	public function __construct(){
		$this->table = 'user_address';
	}

	/**
	 * 获取地区信息
	 * @return array
	 */
	public function getArea()
	{
		return DB::table('area')->get();
	}

	/*
	 * 根据手机号查询是否已添加该地址
	 */
	public function getAddressByTel($tel)
	{
		$data = DB::table($this->table)->where('tel', $tel)->first();

		return $data ? true : false;
	}

	/**
	 * 添加收货地址  设置为默认的,将其他的设置为非默认的
	 * @param user_id   用户id
	 * @param name      收货人姓名
	 * @param mobile    手机号码
	 * @param area      地区
	 * @param address   详细地址
	 * @return    json
	 */

	public function addAddress($user_id, $name, $tel, $district, $address, $head_name)
	{

		$data = array(
			'user_id'    => $user_id,
			'consignee'  => $name,
			'store_name' => $head_name,
			'district'   => $district,
			'address'    => $address,
			'tel'        => $tel,
			'is_default' => 0,
			'create_time'=> date('Y-m-d H:i:s')
		);

		$newId = DB::table($this->table)->insertGetId($data);

		return $newId;
	}


	/**
	 *    获取用户默认收货地址信息
	 * @param        userId        number        用户ID
	 */

	public function getDefaultAddress($ch_user_id)
	{

		$address = DB::table($this->table)
			->select('address_id', 'consignee','store_name','address','district','tel')
			->where('user_id', $ch_user_id)
			->orderBy('is_default', 'DESC')
			->first();

		if(empty($address)){
			return array();
		}
		$address->address = $address->district.$address->address;
		unset($address->district);
		return $address;
	}


	/**
	 * 根据用户id获取收货地址列表
	 * @param user_id   用户id
	 * @return    json
	 */
	public function getAddressList($userId,$offset,$length)
	{
		return DB::table($this->table)
			->where('user_id', $userId)
			->skip($offset)
			->take($length)
			->orderBy('is_default', 'DESC')
			->get();
	}


	/**
	 * 根据收货地址id修改收货信息
	 * @param address_id    收货地址id
	 * @param name        收货人姓名
	 * @param mobile        手机号码
	 * @param area        地区
	 * @param address    详细地址
	 * @return    json
	 */
	public function editAddressByAddressId($user_id, $address_id, $name, $mobile, $area, $address, $isDefault, $store)
	{
		$data = array(
			'consignee'  => $name,
			'store_name' => $store,
			'district'   => $area,
			'address'    => $address,
			'tel'        => $mobile,
			'is_default' => $isDefault
		);

		//设置默认
		if ($isDefault == 1) {
			DB::table($this->table)->where('user_id', $user_id)->where('is_default',1)->update(array('is_default' => 0));
		}

		return DB::table($this->table)
			->where('address_id', $address_id)
			->update($data);
	}


	/*
	 * 设置默认收货地址
	 */
	public function setDefault($user_id,$id){
		$data = array(
			'is_default' => 1
		);

		DB::table($this->table)->where('user_id', $user_id)->where('is_default',1)->update(array('is_default' => 0));

		return DB::table($this->table)
			->where('address_id', $id)
			->update(array('is_default'=>1));

	}

	/*
	 * 判断该收货地址是否存在
	 */
	public function isAddressById($address_id)
	{
		$data = DB::table($this->table)->where('address_id', $address_id)->first();
		return $data ? true : false;
	}

	/*
	 * 判断除该address_id外的手机号是否与修改的手机号冲突
	 *
	 */
	public function issetAddressPhone($address_id, $mobile, $user_id)
	{
		$issetTel = DB::table($this->table)
			->where('address_id', '!=', $address_id)
			->where('tel', $mobile)
			->where('user_id', $user_id)
			->first();
		return $issetTel ? true : false;
	}


	/**
	 * 根据用户id删除收货地址列表
	 * @param user_id   用户id
	 * @return    json
	 */
	public function DeleteAddressByAddressId($address_id, $user_id)
	{
		return DB::table($this->table)
			->where('address_id', $address_id)
			->where('user_id', $user_id)
			->delete();
	}

}
