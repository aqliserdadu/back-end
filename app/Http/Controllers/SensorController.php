<?php

namespace App\Http\Controllers;

use App\Models\ListParameter;
use App\Models\Setting;
use App\Models\Sensor;
use App\Models\Parameter;
use App\Models\KalibrasiSensor;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SensorController extends Controller
{
    protected $wplan;
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->wplan = 'wlp3s0'; // Memberi nilai awal pada variabel
    }

    public function index()
    {
        //
    }

    public function ambilValues(){

        try {

                //ambil data dari database a
                $data = DB::table("tbl_sensor_data")->where("tipe", "manual")->orderBy('dateall', 'desc')->first();
                $ph = $data->pH;
                $tss = $data->tss;
                $nh3n = $data->nh3n;
                $cod = $data->cod;
                $depth = $data->depth;
                $debit = $data->debit;
                $rainfall = $data->rainfall;
                $temperature = $data->temperature;
                $waterpressure = $data->waterpressure;
                $dateall = $data->dateall;
                $tampung = [];

                $ambilParameter = Parameter::all();


                foreach ($ambilParameter as $a) {
                    $parameter = $a['name'];
                    $unit = $a['unit'];

                    switch ($parameter) {
                        case "pH":
                            $value = $ph;
                            break;
                        case "TSS":
                            $value = $tss;
                            break;
                        case "Debit":
                            $value = $debit;
                            break;
                        case "COD":
                            $value = $cod;
                            break;
                        case "NH3-N":
                            $value = $nh3n;
                            break;
                        case "Rainfall":
                            $value = $rainfall;
                            break;
                        case "Depth":
                            $value = $depth;
                            break;
                        case "Temperature":
                            $value = $temperature;
                            break;
                        case "Water Pressure":
                            $value = $waterpressure;
                            break;
                        default:
                            $value = 0;
                            break;
                    }

                    $tampung[] = [
                        'id' => $a['id'],
                        'name' => $parameter,
                        'value' => $value,
                        'unit' => $unit,
                        'dateall' => $dateall
                    ];
                }

            return response()->json(['message' => true, 'data' => $tampung], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 200);
        }

    }




    #Fungsi di Menu Service
    public function loginService(Request $request)
    {

        $user = "abu";
        $pass = "abu";
        $username = $request->input('username');
        $password = $request->input('password');

        if ($username == $user && $pass == $password) {

            return response()->json([
                "status" => true,
                "massage" => "Login Berhasil!",
                "user" => $username
            ], 200);
        } else {

            return response()->json([
                "status" => false,
                'message' => 'Username atau password salah'
            ], 401);
        }
    }
    #Fungsi di Menu Service->ServiceMode
    public function deviceSetting(Request $request)
    {


        try {


            $setting = Setting::first();

            $data = $request->only(['deviceid', 'stationname', 'latitude', 'longitude']);

            if (!$setting) {
                Setting::create($data);
                $message = "created";
            } else {
                $setting->update($data);
                $message = "updated";
            }

            return response()->json([
                "status" => true,
                "message" => $message
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
    public function apiSetting(Request $request)
    {

        try {


            $cek = Setting::first();
            if (!$cek) {
                //jika tidak ada data
                Setting::create([
                    "klhapi" => $request->get('klhapi'),
                    "klhtoken" => $request->get('klhtoken'),
                    "klhstatus" => $request->get('klhstatus') ? 1 : 0,
                    "wqmsapi" => $request->get('wqmsapi'),
                    "wqmstoken" => $request->get('wqmstoken'),
                    "wqmsstatus" => $request->get('wqmsstatus') ? 1 : 0,
                ]);
            } else {

                //jika ada data
                Setting::where('id', $cek->id)->update([

                    "klhapi" => $request->get('klhapi'),
                    "klhtoken" => $request->get('klhtoken'),
                    "klhstatus" => $request->get('klhstatus') ? 1 : 0,
                    "wqmsapi" => $request->get('wqmsapi'),
                    "wqmstoken" => $request->get('wqmstoken'),
                    "wqmsstatus" => $request->get('wqmsstatus') ? 1 : 0,

                ]);
            }



            return response()->json(["status" => true, 'message' => 'Data Berhasil Di Simpan!'], 200);
        } catch (Exception $e) {

            return response()->json(["status" => false, "message" => $e->getMessage()], 500);
        }
    }
    public function emailSetting(Request $request)
    {

        try {


            $cek = Setting::first();
            if (!$cek) {
                //jika tidak ada data
                Setting::create([
                    "stmpserver" => $request->get('stmpserver'),
                    "stmpport" => $request->get('stmpport'),
                    "stmpusername" => $request->get('stmpusername'),
                    "stmppassword" => $request->get('stmppassword'),
                ]);
            } else {

                //jika ada data
                Setting::where('id', $cek->id)->update([

                    "stmpserver" => $request->get('stmpserver'),
                    "stmpport" => $request->get('stmpport'),
                    "stmpusername" => $request->get('stmpusername'),
                    "stmppassword" => $request->get('stmppassword'),

                ]);
            }



            return response()->json(["status" => true, 'message' => 'Data Berhasil Di Simpan!'], 200);
        } catch (Exception $e) {

            return response()->json(["status" => false, "message" => $e->getMessage()], 500);
        }
    }
    public function ambilParameterList()
    {

        try {

            //cek table kalbrasi ada apa tidak
            $data = KalibrasiSensor::all();
            return response()->json(['data' => $data, "status" => true], 200);
        } catch (\Exception $e) {

            return response()->json(["status" => false, "message" => $e->getMessage()], 500);
        }
    }
    public function manualReadingSensor(Request $request)
    {

        $dataGet = $request->all();

        $cekAutoMeasure = Setting::first();
        $cek = (int) $cekAutoMeasure->automeasure;
        if($cek == 1 ){
            return response()->json(["status"=>false,"massage"=>"Harap matikan fungsi automeasure, sebelum manual reading!"],500);
        }

        try {

            $output = exec('python3 /var/www/html/project/spas-main/manualReading.py', $output, $return_code);
            if ($return_code === 0) {
                //ambil data dari database a
                $data = DB::table("tbl_sensor_data")->where("tipe", "manual")->orderBy('dateall', 'desc')->first();
                $ph = $data->pH;
                $tss = $data->tss;
                $nh3n = $data->nh3n;
                $cod = $data->cod;
                $depth = $data->depth;
                $debit = $data->debit;
                $rainfall = $data->rainfall;
                $temperature = $data->temperature;
                $waterpressure = $data->waterpressure;
                $tampung = [];
                foreach ($dataGet as $a) {
                    $parameter = $a['name'];
                    $offset = (int) $a['offset'];

                    switch ($parameter) {
                        case "pH":
                            $value = $ph;
                            break;
                        case "TSS":
                            $value = $tss;
                            break;
                        case "Debit":
                            $value = $debit;
                            break;
                        case "COD":
                            $value = $cod;
                            break;
                        case "NH3-N":
                            $value = $nh3n;
                            break;
                        case "Rainfall":
                            $value = $rainfall;
                            break;
                        case "Depth":
                            $value = $depth;
                            break;
                        case "Temperature":
                            $value = $temperature;
                            break;
                        case "Water Pressure":
                            $value = $waterpressure;
                            break;
                        default:
                            $value = 0;
                            break;
                    }

                    $final = (float)$value + $offset;

                    $tampung[] = [
                        'id' => $a['id'],
                        'name' => $parameter,
                        'value' => $value,
                        'offset' => $offset,
                        'final' => $final
                    ];
                }

                return response()->json(['message' => true, 'data' => $tampung], 200);
            } else {
                return response()->json(['message' => "Script Python Gagal Di Jalankan"], 200);
            }
        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 200);
        }
    }

    public function saveKalibrasiSensor(Request $request){

        try {


            $data = $request->all();
            if ($data){
                foreach($data as $in){
                    KalibrasiSensor::where("name",$in['name'])->update(['offset' => $in['offset']]);
                }
            }

            return response()->json(["status" => true, 'message' => 'Data Berhasil Di Simpan!'], 200);
        } catch (Exception $e) {

            return response()->json(["status" => false, "message" => $e->getMessage()], 500);
        }
    }

    #End Service

    #Fungsi di Menu Setting
    public function ambilSetting()
    {

        try {
            $data = Setting::first();
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(["massage" => $e->getMessage()], 500);
        }
    }

    public function saveSetting(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'interval' => 'required|integer|min:1',
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }


            $cek = Setting::first();
            if (!$cek) {
                //jika tidak ada data
                Setting::create([
                    "interval" => $request->get('interval'),
                    "email" => $request->get('email'),
                    "automeasure" => $request->get('automeasure'),
                ]);
            } else {

                //jika ada data
                Setting::where('id', $cek->id)->update([

                    "interval" => $request->get('interval'),
                    "email" => $request->get('email'),
                    "automeasure" => $request->get('automeasure'),

                ]);
            }



            return response()->json(["status" => true, 'message' => 'Data Berhasil Di Simpan!'], 200);
        } catch (Exception $e) {

            return response()->json(["status" => false, "message" => $e->getMessage()], 500);
        }
    }
    public function ambilSensor()
    {

        try {

            $result = DB::table('tbl_sensor')
                ->join('tbl_parameter', 'tbl_sensor.id', '=', 'tbl_parameter.idsensor')
                ->select(
                    'tbl_sensor.id',
                    'tbl_parameter.tipe',
                    'tbl_sensor.sensorname',
                    DB::raw('GROUP_CONCAT(tbl_parameter.name SEPARATOR ",") as parameters')
                )
                ->groupBy('tbl_sensor.id', 'tbl_parameter.tipe', 'tbl_sensor.sensorname')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $result,
            ], 200);
        } catch (\Exception $err) {

            return response()->json([
                'status' => false,
                'messages' => $err->getMessage(),
            ], 500);
        }
    }
    public function deleteSensor($id)
    {

        DB::beginTransaction();

        try {
            $sensor = Sensor::findOrFail($id);

            // Hapus semua parameter yang terkait
            Parameter::where('idsensor', $id)->delete();

            // Hapus sensor
            $sensor->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Sensor dan parameter berhasil dihapus!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus sensor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #Fungsi Menu Setting->AddRainSensor
    public function ambilRainSensor()
    {

        try {

            $result = DB::table("tbl_sensor")
                ->join("tbl_parameter", "tbl_parameter.idsensor", "=", "tbl_sensor.id")
                ->select("tbl_sensor.sensorname", "tbl_parameter.resolution")
                ->where('tbl_sensor.port', 'GPIO')->get();
            return response()->json([
                'status' => true,
                'data' => $result,
            ], 200);
        } catch (\Exception $err) {

            return response()->json([
                'status' => false,
                'messages' => $err->getMessage(),
            ], 500);
        }
    }
    public function saveRainSensor(Request $request)
    {


        $data = $request->all();
        $name = $data['name'];
        $tipping = $data['tipping'];

        // Cek sudah ada di database
        $existingParameter = Parameter::where('name', "Rainfall")->first();

        if ($existingParameter && $existingParameter->tipe == "modbus") {

            return response()->json([
                'status' => false,
                'message' => "Parameter Raninfall Telah Di Dengan Modbus!",
            ], 400);
        }

        if ($existingParameter && $existingParameter->tipe == "GPIO") {
            //hapus jika ada data
            Sensor::destroy($existingParameter->idsensor);
            Parameter::destroy($existingParameter->id);
            KalibrasiSensor::where('idsensor', $existingParameter->idsensor)->delete();
        }


        DB::beginTransaction();
        try {


            $header = Sensor::create([

                "sensorname" => $name,
                "port" => "GPIO",
                "baudrate" => "",
                "slaveid" => "",
                "functioncode" => "",
                "databits" => "",
                "stopbits" => "",
                "parity" => "",
                "length" => "",
                "address" => "",
                "crc" => "",
                "metode" => "",

            ]);

            $parameter = Parameter::create([
                'idsensor' => $header->id,
                'tipe' => 'GPIO',
                'name' => 'Rainfall',
                'resolution' => $tipping,
                'parsing' => '',
                'post' => '',
                'unit' => '',
            ]);

            KalibrasiSensor::create([
                'idsensor' => $header->id,
                'name' => 'Rainfall',

            ]);

            // Commit transaksi jika berhasil
            DB::commit();

            return response()->json([
                'message' => 'Post and details created successfully!',
                'header_id' => $header->id,
                'parametersData' => 'Rainfall',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Mengembalikan response error
            return response()->json([
                'message' => 'An error occurred while creating post or details.',
                'error' => $e->getMessage(),
            ], 500);
        }

        //return response()->json($data,200);

    }


    #Fungsi Menu Setting->AddSensor
    public function ambilPort()
    {

        $ports = array_merge(
            //glob("/dev/ttyS*"),  // Port serial bawaan
            glob("/dev/ttyUSB*"), // USB-Serial
            glob("/dev/ttyACM*")  // Arduino dan perangkat lain
        );

        //$serial_ports = $ports;
        $serial_ports = array("/dev/ttyUSB1", "/dev/ttyUSB9");

        if (!empty($serial_ports)) {

            return response()->json(["data" => $serial_ports], 200, [], JSON_UNESCAPED_SLASHES);
        } else {

            return response()->json(["data" => []], 200);
        }


        // $output = shell_exec("ls /dev/ttyUSB* 2>/dev/null");

        // // Ubah hasil menjadi array
        // $ports = explode("\n", trim($output));

        // // Bersihkan output JSON
        // return response()->json([
        //     'data' => empty($output) ? [] : array_map('trim', $ports)
        // ], 200, [], JSON_UNESCAPED_SLASHES);

    }
    public function ambilParameter()
    {

        try {
            $parameter = Parameter::pluck("name")->toArray();
            $data = ListParameter::select('parameter')->whereNotIn('parameter', $parameter)->get();
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            Log::error('Gagal mengambil data parameter: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.'
            ], 500);
        }
    }
    public function saveSensor(Request $request)
    {


        $data = $request->all();
        $sensorName = $data['sensorSettings']['sensorName'];
        $dataBits = $data['sensorSettings']['dataBits'];
        $port = $data['sensorSettings']['port'];
        $stopBits = $data['sensorSettings']['stopBits'];
        $baudRate = $data['sensorSettings']['baudRate'];
        $parity = $data['sensorSettings']['parity'];
        $slaveID = $data['sensorSettings']['slaveID'];
        $length = $data['sensorSettings']['length'];
        $functionCode = $data['sensorSettings']['functionCode'];
        $address = $data['sensorSettings']['address'];
        $crc = $data['sensorSettings']['crc'];
        $metode = $data['sensorSettings']['metode'];
        $parameters = $data['parameters'];


        // Cek apakah port sudah ada di database
        $existingSensor = Sensor::where('port', $port)->first();

        if ($existingSensor) {
            return response()->json([
                'status' => false,
                'message' => 'Port sudah terpakai!',
            ], 400);
        }



        DB::beginTransaction();
        try {


            $header = Sensor::create([

                "sensorname" => $sensorName,
                "port" => $port,
                "baudrate" => $baudRate,
                "slaveid" => $slaveID,
                "functioncode" => $functionCode,
                "databits" => $dataBits,
                "stopbits" => $stopBits,
                "parity" => $parity,
                "length" => $length,
                "address" => $address,
                "crc" => $crc,
                "metode" => $metode,

            ]);


            $parametersData = array_map(function ($parameter) use ($header) {
                $param = Parameter::create([
                    'idsensor' => $header->id,
                    'name' => $parameter['parameterName'],
                    'parsing' => $parameter['dataParsing'],
                    'post' => $parameter['postProcessing'],
                    'unit' => $parameter['unit'],
                ]);
                return $param;
            }, $parameters);

            $kalibrasiData = array_map(function ($parameter) use ($header) {
                $param = KalibrasiSensor::create([
                    'idsensor' => $header->id,
                    'name' => $parameter['parameterName'],
                ]);
                return $param;
            }, $parameters);


            // Commit transaksi jika berhasil
            DB::commit();

            return response()->json([
                'message' => 'Post and details created successfully!',
                'header_id' => $header->id,
                'parametersData' => $parametersData,
                'kalibrasiData' => $kalibrasiData
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Mengembalikan response error
            return response()->json([
                'message' => 'An error occurred while creating post or details.',
                'error' => $e->getMessage(),
            ], 500);
        }



        //return response()->json($data,200);

    }

    #Fungsi Menu Setting->EditSensor
    public function editSensor(Request $request)
    {

        $id = $request->get('id');
        try {

            $dataSensor = Sensor::where('id', $id)->get();
            $dataParameter = Parameter::where('idsensor', $id)->get();

            return response()->json(["status" => true, "sensor" => $dataSensor, "parameter" => $dataParameter], 200);
        } catch (\Exception $err) {

            return response()->json(["status" => true, "mssage" => $err->getMessage()], 200);
        }
    }
    public function ambilParameterEdit()
    {

        try {

            $data = ListParameter::all();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            Log::error('Gagal mengambil data parameter: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.'
            ], 500);
        }
    }
    public function updateSensor(Request $request, $id)
    {
        $data = $request->all();
        $sensorSettings = $data['params']['sensorSettings'];
        $parameters = $data['params']['parameters'];

        $sensor = Sensor::findOrFail($id);

        // Cek apakah port sudah dipakai sensor lain
        $existingSensor = Sensor::where('port', $sensorSettings['port'])
            ->where('id', '!=', $id)
            ->first();

        if ($existingSensor) {
            return response()->json([
                'status' => false,
                'message' => 'Port sudah terpakai oleh sensor lain!',
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Update sensor utama
            $sensor->update([
                "sensorname" => $sensorSettings['sensorName'],
                "port" => $sensorSettings['port'],
                "baudrate" => $sensorSettings['baudRate'],
                "slaveid" => $sensorSettings['slaveID'],
                "functioncode" => $sensorSettings['functionCode'],
                "databits" => $sensorSettings['dataBits'],
                "stopbits" => $sensorSettings['stopBits'],
                "parity" => $sensorSettings['parity'],
                "length" => $sensorSettings['length'],
                "address" => $sensorSettings['address'],
                "crc" => $sensorSettings['crc'],
                "metode" => $sensorSettings['metode'],
            ]);

            // Hapus parameter lama
            Parameter::where('idsensor', $id)->delete();
            KalibrasiSensor::where('idsensor', $id)->delete();

            // Masukkan parameter baru
            $parametersData = array_map(function ($parameter) use ($id) {
                return Parameter::create([
                    'idsensor' => $id,
                    'name' => $parameter['parameterName'],
                    'parsing' => $parameter['dataParsing'],
                    'post' => $parameter['postProcessing'],
                    'unit' => $parameter['unit'],
                ]);
            }, $parameters);

            // Masukkan parameter baru
            $kalibrasiData = array_map(function ($parameter) use ($id) {
                return KalibrasiSensor::create([
                    'idsensor' => $id,
                    'name' => $parameter['parameterName'],
                ]);
            }, $parameters);



            DB::commit();

            return response()->json([
                'message' => 'Sensor dan parameter berhasil diupdate!',
                'sensor_id' => $id,
                'parameters' => $parametersData,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat update sensor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #End Setting

    #Utilites Network Setting

    public function cekDevice()
    {

        try {
            $command = "nmcli device status";

            // Menjalankan perintah dan mendapatkan output
            $output = shell_exec($command);

            if ($output === null) {
                throw new \Exception("Gagal menjalankan perintah shell.");
            }

            // Mengecek apakah ada perangkat yang terhubung dan tipe koneksinya
            if (strpos($output, 'wifi') !== false && strpos($output, 'connected') !== false) {
                return response()->json(["status" => true, "device" => "wifi"], 200);
            } else if (strpos($output, 'ethernet') !== false && strpos($output, 'connected') !== false) {
                return response()->json(["status" => true, "device" => "lan"], 200);
            } else {
                return response()->json(["status" => false, "message" => "Tidak ada jaringan yang terhubung"], 500);
            }
        } catch (\Throwable $e) {
            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan: " . $e->getMessage()
            ], 500);
        }
    }
    public function cekKoneksi()
    {
        // Cek resolusi DNS untuk google.com
        $url = "www.google.com";
        $cekInet = false;
        $connected = @fsockopen($url, 80); // Coba koneksi ke Google pada port 80 (HTTP)

        if ($connected) {
            $cekInet = true;
            fclose($connected);
            return response()->json(["status" => $cekInet, "message" => "Koneksi Jaringan Terhubung ke internet"], 200);
        }

        return response()->json(["status" => $cekInet, "message" => "Tidak ada koneksi internet"], 500);
    }
    public function ambilWifi()
    {

        $wplan = $this->wplan;
        $output = shell_exec("/sbin/iwlist " . escapeshellarg($wplan) . " scanning");

        // Jika perintah gagal dijalankan
        if (!$output) {

            return response()->json(['status' => false, 'data' => [], 'message' => 'Tidak dapat memindai jaringan WiFi.'], 500);
        }

        try {
            // Menggunakan regex untuk mengekstrak ESSID (nama jaringan WiFi)
            preg_match_all('/ESSID:"([^"]+)"/', $output, $matches);

            // Jika ada jaringan WiFi yang ditemukan
            if (!empty($matches[1])) {
                $wifiList = $matches[1]; // Menyimpan SSID ke dalam array

                // Mengonversi array ke format JSON
                return response()->json(['status' => true, 'data' => $wifiList], 200, [], JSON_PRETTY_PRINT);
            } else {
                // Jika tidak ada jaringan WiFi yang ditemukan
                return response()->json(['status' => false, 'data' => [], 'message' => 'Tidak ada WiFi yang ditemukan.'], 500);
            }
        } catch (Exception $e) {
            // Menangani error yang mungkin terjadi selama pemrosesan
            return response()->json(['status' => false, 'data' => [], 'message' => $e->getMessage()], 500);
        }
    }
    public function konekWifi(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'selectedWifi' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $ssid = $request->get('selectedWifi');
            $password = $request->get('password');
            $command = "nmcli dev wifi connect '$ssid' password '$password'";

            $output = shell_exec($command);

            // Memeriksa apakah perintah berhasil
            if ($output) {

                return response()->json(['status' => true, 'message' => 'Berhasil terhubung ke WiFi: ' . $ssid], 201);
            } else {
                return response()->json(['status' => false, 'message' => 'Gagal terhubung ke WiFi.'], 500);
            }
        } catch (Exception $e) {

            return response()->json(["status" => false, "message" => $e->getMessage()], 500);
        }
    }
    public function detailConn()
    {
        // Get the SSID (connected WiFi network) using the `iwgetid` command

        $device = $this->wplan;
        $ssidCommand = 'sudo iwgetid -r';
        $ssid = trim(shell_exec($ssidCommand));

        if (empty($ssid)) {
            $ssid = "";
        }

        // Get the IP address of the device using `hostname -I`
        $ipCommand = "sudo ifconfig $device | grep 'inet ' | awk '{print $2}'";
        $ipAddress = trim(shell_exec($ipCommand));

        if (empty($ipAddress)) {
            return response()->json([
                'status' => false,
                'message' => 'No IP address found.'
            ], 500);
        }

        // Get the Netmask of the device using `ifconfig`
        $netmaskCommand = "sudo ifconfig $device | grep 'inet ' | awk '{print $4}'";
        $netmask = trim(shell_exec($netmaskCommand));

        // Get the Gateway IP address using `ip route` command
        $gatewayCommand = "ip route | grep default | awk '{print $3}'";
        $gateway = trim(shell_exec($gatewayCommand));

        // Get the DNS servers using `resolv.conf`
        $dnsCommand = "cat /etc/resolv.conf | grep nameserver | awk '{print $2}'";
        $dns = shell_exec($dnsCommand);
        $dns = array_map('trim', explode("\n", $dns)); // Split DNS addresses into an array

        return response()->json([
            'status' => true,
            'message' => 'Successfully fetched WiFi details',
            'wifi' => [
                'SSID' => $ssid,
                'IP' => $ipAddress,
                'Netmask' => $netmask,
                'Gateway' => $gateway,
                'DNS' => $dns
            ]
        ], 200);
    }
    #End Utilites


}
