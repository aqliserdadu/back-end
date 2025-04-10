<?php

namespace App\Http\Controllers;

use App\Models\ListParameter;
use App\Models\Setting;
use App\Models\Sensor;
use App\Models\Parameter;
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


            $cek = Setting::find(1);
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

    public function ambilSetting()
    {

        try {
            $data = Setting::find(1);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(["massage" => $e->getMessage()], 500);
        }
    }



    //function untuk add sensor di menu setting

    public function ambilPort()
    {

        $ports = array_merge(
            //glob("/dev/ttyS*"),  // Port serial bawaan
            glob("/dev/ttyUSB*"), // USB-Serial
            glob("/dev/ttyACM*")  // Arduino dan perangkat lain
        );

        $serial_ports = $ports;

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
            $data = ListParameter::select('parameter')->whereNotIn('parameter',$parameter)->get();
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


    public function saveSensor(Request $request){


        $data = $request->all();
        //$sensorSettings = $data['sensorSettings'];
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
        try{

            $header = Sensor::create([

                    "sensorname" => $sensorName,
                    "port" => $port,
                    "baudrate" => $baudRate,
                    "slaveid" => $slaveID,
                    "functioncode" => $functionCode,
                    "databits" => $dataBits,
                    "stopbits" => $stopBits,
                    "partiy" => $parity,
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

            // Commit transaksi jika berhasil
            DB::commit();

            return response()->json([
                'message' => 'Post and details created successfully!',
                'header_id' => $header->id,
                'parametersData' => $parametersData,
            ]);


        }catch(\Exception $e){
            DB::rollBack();

            // Mengembalikan response error
            return response()->json([
                'message' => 'An error occurred while creating post or details.',
                'error' => $e->getMessage(),
            ], 500);
        }



        //return response()->json($data,200);

    }

    public function editSensor(Request $request){

        $id = $request->get('id');
        try{

            $dataSensor = Sensor::where('id',$id)->get();
            $dataParameter = Parameter::where('idsensor',$id)->get();

            return response()->json(["status"=>true,"sensor"=>$dataSensor,"parameter"=>$dataParameter],200);

        }catch(\Exception $err){

            return response()->json(["status"=>true,"mssage" => $err->getMessage()],200);

        }

    }

    public function ambilSensor(){

       try{

            $result = DB::table('tbl_sensor')
            ->join('tbl_parameter', 'tbl_sensor.id', '=', 'tbl_parameter.idsensor')
            ->select('tbl_sensor.id','tbl_sensor.sensorname', DB::raw('GROUP_CONCAT(tbl_parameter.name SEPARATOR ",") as parameters'))
            ->groupBy('tbl_sensor.id')
            ->get();

            return response()->json([
                'status' => true,
                'data' => $result,
            ],200);

       }catch(\Exception $err){

            return response()->json([
                'status' => false,
                'messages' => $err->getMessage(),
            ],500);
       }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // konfigurasi jaringan wifi

    public function cekDevice()
    {

        $command = "nmcli device status";

        // Menjalankan perintah dan mendapatkan output
        $output = shell_exec($command);

        // Mengecek apakah ada perangkat yang terhubung dan tipe koneksinya
        if (strpos($output, 'wifi') !== false && strpos($output, 'connected') !== false) {
            return response()->json(["status" => true, "device" => "wifi"], 200);
        } else if (strpos($output, 'ethernet') !== false && strpos($output, 'connected') !== false) {
            return response()->json(["status" => true, "device" => "lan"], 200);
        } else {
            return response()->json(["status" => false, "message" => "Tidak ada jaringan yang terhubung"], 500);
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
            return response()->json(["status" => $cekInet,"message" => "Koneksi Jaringan Terhubung ke internet"], 200);
        }

        return response()->json(["status" => $cekInet,"message" => "Tidak ada koneksi internet"], 500);
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
                $ssid="";
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
}
