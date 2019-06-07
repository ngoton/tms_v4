<?php
Class vehicleanalyticsController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Quản lý hoạt động xe';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

            $xe = isset($_POST['xe']) ? $_POST['xe'] : null;

            $mooc = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;

            $xem = isset($_POST['vong']) ? $_POST['vong'] : null;
        }

        else{

            $keyword = "";

            $xe = 0;

            $mooc = 0;

            $batdau = '01-01-'.date('Y');

            $ketthuc = date('t-m-Y');

            $xem = 0;

        }

        $numberMonth = $this->countMonth($batdau,$ketthuc);
        $countMonth = date('m',strtotime($batdau))+$numberMonth;


        $vehicle_model = $this->model->get('vehicleModel');
        $romooc_model = $this->model->get('romoocModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicle_lists'] = $vehicles;

        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));
        $this->view->data['romooc_lists'] = $romoocs;
        
        $this->view->data['xe'] = $xe;
        $this->view->data['mooc'] = $mooc;

        $this->view->data['keyword'] = $keyword;
        $this->view->data['batdau'] = $batdau;
        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['xem'] = $xem;
        $this->view->data['countMonth'] = $countMonth;
        $this->view->data['numberMonth'] = $numberMonth;

        $this->view->data['page'] = 1;
        $this->view->data['order_by'] = "";
        $this->view->data['order'] = "";

        $data = array(
            'where' => '1=1',
            'order_by' => 'vehicle_number',
            'order' => 'ASC',
            );

        if ($xe > 0) {
            $data['where'] .= ' AND vehicle_id = '.$xe;
        }
        
        if ($keyword != '') {
            $search = ' AND ( vehicle_number LIKE "%'.$keyword.'%"  )';
            $data['where'] .= $search;
        }
        
        $vehicles = $vehicle_model->getAllVehicle($data);
        $this->view->data['vehicles'] = $vehicles;

        $data = array(
            'where' => '1=1',
            'order_by' => 'romooc_number',
            'order' => 'ASC',
            );

        if ($mooc > 0) {
            $data['where'] .= ' AND romooc_id = '.$mooc;
        }
        
        if ($keyword != '') {
            $search = ' AND ( romooc_number LIKE "%'.$keyword.'%"  )';
            $data['where'] .= $search;
        }
        
        $romoocs = $romooc_model->getAllVehicle($data);
        $this->view->data['romoocs'] = $romoocs;


        $shipment_model = $this->model->get('shipmentModel');
        $road_model = $this->model->get('roadModel');

        $data_vehicle = array();

        foreach ($vehicles as $vehicle) {
           
            $data_ship = array(
                'where'=>'vehicle = '.$vehicle->vehicle_id.' AND shipment_date >= '.strtotime($batdau).' AND shipment_date <= '.strtotime($ketthuc),
            );
            $shipments = $shipment_model->getAllShipment($data_ship);
            foreach ($shipments as $ship) {
                $check_sub = 1;
                if ($ship->shipment_sub==1) {
                   $check_sub = 0;
                }
                if ($xem == 0) {
                    $data_vehicle[$vehicle->vehicle_id]['ship'] = isset($data_vehicle[$vehicle->vehicle_id]['ship'])?$data_vehicle[$vehicle->vehicle_id]['ship']+1:1;
                    $data_vehicle[$vehicle->vehicle_id]['oil'] = isset($data_vehicle[$vehicle->vehicle_id]['oil'])?$data_vehicle[$vehicle->vehicle_id]['oil']+$ship->shipment_oil:$ship->shipment_oil;
                    $data_vehicle[$vehicle->vehicle_id]['revenue'] = isset($data_vehicle[$vehicle->vehicle_id]['revenue'])?$data_vehicle[$vehicle->vehicle_id]['revenue']+$ship->shipment_revenue+$ship->shipment_charge_excess:$ship->shipment_revenue+$ship->shipment_charge_excess;

                    $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));
                    foreach ($roads as $road) {
                        $data_vehicle[$vehicle->vehicle_id]['km'] = isset($data_vehicle[$vehicle->vehicle_id]['km'])?$data_vehicle[$vehicle->vehicle_id]['km']+$road->road_km*$check_sub:$road->road_km*$check_sub;
                    }
                }
                else{
                    $thang = (int)date('m',$ship->shipment_date).'-'.date('Y',$ship->shipment_date);
                    $data_vehicle[$vehicle->vehicle_id]['ship'][$thang] = isset($data_vehicle[$vehicle->vehicle_id]['ship'][$thang])?$data_vehicle[$vehicle->vehicle_id]['ship'][$thang]+1:1;
                    $data_vehicle[$vehicle->vehicle_id]['oil'][$thang] = isset($data_vehicle[$vehicle->vehicle_id]['oil'][$thang])?$data_vehicle[$vehicle->vehicle_id]['oil'][$thang]+$ship->shipment_oil:$ship->shipment_oil;
                    $data_vehicle[$vehicle->vehicle_id]['revenue'][$thang] = isset($data_vehicle[$vehicle->vehicle_id]['revenue'][$thang])?$data_vehicle[$vehicle->vehicle_id]['revenue'][$thang]+$ship->shipment_revenue+$ship->shipment_charge_excess:$ship->shipment_revenue+$ship->shipment_charge_excess;

                    $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));
                    foreach ($roads as $road) {
                        $data_vehicle[$vehicle->vehicle_id]['km'][$thang] = isset($data_vehicle[$vehicle->vehicle_id]['km'][$thang])?$data_vehicle[$vehicle->vehicle_id]['km'][$thang]+$road->road_km*$check_sub:$road->road_km*$check_sub;
                    }
                }
                
            }

        }

        $data_romooc = array();

        foreach ($romoocs as $romooc) {
           

            $data_ship = array(
                'where'=>'romooc = '.$romooc->romooc_id.' AND shipment_date >= '.strtotime($batdau).' AND shipment_date <= '.strtotime($ketthuc),
            );
            $shipments = $shipment_model->getAllShipment($data_ship);
            foreach ($shipments as $ship) {
                $check_sub = 1;
                if ($ship->shipment_sub==1) {
                   $check_sub = 0;
                }
                if ($xem == 0) {
                    $data_romooc[$romooc->romooc_id]['ship'] = isset($data_romooc[$romooc->romooc_id]['ship'])?$data_romooc[$romooc->romooc_id]['ship']+1:1;
                    $data_romooc[$romooc->romooc_id]['revenue'] = isset($data_romooc[$romooc->romooc_id]['revenue'])?$data_romooc[$romooc->romooc_id]['revenue']+$ship->shipment_revenue+$ship->shipment_charge_excess:$ship->shipment_revenue+$ship->shipment_charge_excess;
                    
                    $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));
                    foreach ($roads as $road) {
                        $data_romooc[$romooc->romooc_id]['km'] = isset($data_romooc[$romooc->romooc_id]['km'])?$data_romooc[$romooc->romooc_id]['km']+$road->road_km*$check_sub:$road->road_km*$check_sub;
                    }
                }
                else{
                    $thang = (int)date('m',$ship->shipment_date).'-'.date('Y',$ship->shipment_date);

                    $data_romooc[$romooc->romooc_id]['ship'][$thang] = isset($data_romooc[$romooc->romooc_id]['ship'][$thang])?$data_romooc[$romooc->romooc_id]['ship'][$thang]+1:1;
                    $data_romooc[$romooc->romooc_id]['revenue'][$thang] = isset($data_romooc[$romooc->romooc_id]['revenue'][$thang])?$data_romooc[$romooc->romooc_id]['revenue'][$thang]+$ship->shipment_revenue+$ship->shipment_charge_excess:$ship->shipment_revenue+$ship->shipment_charge_excess;
                    
                    $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));
                    foreach ($roads as $road) {
                        $data_romooc[$romooc->romooc_id]['km'][$thang] = isset($data_romooc[$romooc->romooc_id]['km'][$thang])?$data_romooc[$romooc->romooc_id]['km'][$thang]+$road->road_km*$check_sub:$road->road_km*$check_sub;
                    }
                }
                
            }

        }
        

        $this->view->data['data_vehicle'] = $data_vehicle;

        $this->view->data['data_romooc'] = $data_romooc;

        
        $this->view->show('vehicleanalytics/index');
    }

   public function countMonth($from,$to){
    $ts1 = strtotime($from);
    $ts2 = strtotime($to);

    $year1 = date('Y', $ts1);
    $year2 = date('Y', $ts2);

    $month1 = date('m', $ts1);
    $month2 = date('m', $ts2);

    return $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
   }


}
?>