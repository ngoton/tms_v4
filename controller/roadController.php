<?php

Class roadController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->road) || json_decode($_SESSION['user_permission_action'])->road != "road") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý định mức';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

            $status = isset($_POST['vong']) ? $_POST['vong'] : null;

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;
            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

            $di = isset($_POST['xe']) ? $_POST['xe'] : null;
            $den = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'road_from ASC, road_to';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

            $status = 1;

            $batdau = 0;
            $ketthuc = 0;

            $di = 0;
            $den = 0;

        }

        $oil_model = $this->model->get('oilModel');
        $oils = $oil_model->getAllOil();
        $this->view->data['oils'] = $oils;

        $join = array('table'=>'oil', 'where'=>'road.way = oil.oil_id');

        $road_model = $this->model->get('roadModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $qr = 'SELECT * FROM road WHERE 1=1 ';

        if ($batdau > 0) {
            $qr .= ' AND road_from = '.$batdau;
        }

        if ($ketthuc > 0) {
            $qr .= ' AND road_to = '.$ketthuc;
        }

        $qr .= ' GROUP BY road_from, road_to ORDER BY road_from ASC, road_to ASC';

        $tongsodong = count($road_model->queryRoad($qr));

        $tongsotrang = ceil($tongsodong / $sonews);

        $qr .= ', '.$order_by.' '.$order.' LIMIT '.$x.','.$sonews;

        $road_warehouses = $road_model->queryRoad($qr);
        $this->view->data['road_warehouses'] = $road_warehouses;

        $this->view->data['page'] = $page;

        $this->view->data['order_by'] = $order_by;

        $this->view->data['order'] = $order;

        $this->view->data['keyword'] = $keyword;

        $this->view->data['limit'] = $limit;

        $this->view->data['pagination_stages'] = $pagination_stages;

        $this->view->data['tongsotrang'] = $tongsotrang;

        $this->view->data['sonews'] = $sonews;

        $this->view->data['status'] = $status;

        $this->view->data['batdau'] = $batdau;
        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['di'] = $di;
        $this->view->data['den'] = $den;

        $roads = array();
        foreach ($road_warehouses as $r) {
            

            $data = array(
                'where'=>'road_from = '.$r->road_from.' AND road_to = '.$r->road_to,
                );

            if ($di > 0) {
                $data['where'] .= ' AND route_from = '.$di;
            }

            if ($den > 0) {
                $data['where'] .= ' AND route_to = '.$den;
            }

            

            if ($di > 0) {
                $data['where'] .= ' AND route_from = '.$di;
            }

            if ($den > 0) {
                $data['where'] .= ' AND route_to = '.$den;
            }


            if ($keyword != '') {

                $search = ' AND ( road_from in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                            OR road_to in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 
                            OR route_from in (SELECT route_id FROM route WHERE route_name LIKE "%'.$keyword.'%" )
                            OR route_to in (SELECT route_id FROM route WHERE route_name LIKE "%'.$keyword.'%" ) )';

                $data['where'] .= $search;

            }

            $roads[$r->road_from][$r->road_to] = $road_model->getAllRoad($data,$join);
            
        }

        $this->view->data['roads'] = $roads;

        

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        

        $this->view->data['places'] = $places;



        $place_data = array();

        foreach ($places as $place) {

            $place_data['place_id'][$place->place_id] = $place->place_id;

            $place_data['place_name'][$place->place_id] = $place->place_name;

        }

        

        $this->view->data['place'] = $place_data;


        $route_model = $this->model->get('routeModel');

        $routes = $route_model->getAllPlace(array('order_by'=>'route_name','order'=>'ASC'));

        

        $this->view->data['routes'] = $routes;



        $route_data = array();

        foreach ($routes as $route) {

            $route_data['route_id'][$route->route_id] = $route->route_id;

            $route_data['route_name'][$route->route_id] = $route->route_name;

        }

        

        $this->view->data['route'] = $route_data;




        $this->view->data['lastID'] = isset($road_model->getLastRoad()->road_id)?$road_model->getLastRoad()->road_id:0;

        

        $this->view->show('road/index');

    }



    public function bridgecost(){

        if(isset($_POST['road'])){

            

            $bridge_cost_model = $this->model->get('bridgecostModel');



            $join = array('table'=>'toll','where'=>'bridge_cost.toll_booth = toll.toll_id');

            $data = array(

                'where' => 'road = '.$_POST['road'],

            );

            $bridgecosts = $bridge_cost_model->getAllBridgecost($data,$join);



            $str = "";

            if (!$bridgecosts) {

                $str .= '<tr class="'.$_POST['road'].'">';

                $str .= '<td><input type="checkbox"  name="chk"></td>';

                $str .= '<td><table style="width: 100%">';

                $str .= '<tr class="'.$_POST['road'] .'">';

                $str .= '<td>Trạm thu phí</td>';

                $str .= '<td><input type="text" autocomplete="off" class="toll_booth_name" name="toll_booth_name[]" placeholder="Nhập tên hoặc * để chọn" >';

                $str .= '<ul class="name_list_id"></ul></td>';

                $str .= '<td>Giá vé</td>';

                $str .= '<td><input style="width:120px" type="text" class="toll_booth_cost number" name="toll_booth_cost[]"><input type="checkbox" class="check_vat" name="check_vat[]" value="1"> VAT</td></tr>';

                $str .= '<tr><td>MST</td>';

                $str .= '<td><input type="text" class="toll_mst" name="toll_mst[]"></td>';

                $str .= '<td>Mẫu số</td>';

                $str .= '<td><input style="width:120px" type="text" class="toll_symbol" name="toll_symbol[]"></td></tr>';

                $str .= '<tr><td>Loại</td>';

                $str .= '<td><select style="width:200px" class="toll_type" name="toll_type[]"><option value="1">Vé thu phí</option><option value="2">Cước đường bộ</option></select></td>';

                $str .= '</tr></table></td></tr>';

            }

            else{

                foreach ($bridgecosts as $v) {

                    $type1 = $v->toll_type==1?'selected="selected"':null;

                    $type2 = $v->toll_type==2?'selected="selected"':null;

                    $checked = $v->check_vat==1?'checked="checked"':null;

                    $str .= '<tr class="'.$_POST['road'].'">';

                    $str .= '<td><input type="checkbox" alt="'.$v->bridge_cost_id.'"  name="chk" tabindex="'.$v->toll_booth.'" data="'.$v->road.'" title="'.$v->toll_booth_cost.'" ></td>';

                    $str .= '<td><table style="width: 100%">';

                    $str .= '<tr class="'.$_POST['road'] .'">';

                    $str .= '<td>Trạm thu phí</td>';

                    $str .= '<td><input type="text" autocomplete="off" class="toll_booth_name" name="toll_booth_name[]" placeholder="Nhập tên hoặc * để chọn" data="'.$v->toll_booth.'" value="'.$v->toll_name.'" >';

                    $str .= '<ul class="name_list_id"></ul></td>';

                    $str .= '<td>Giá vé</td>';

                    $str .= '<td><input style="width:120px" type="text" class="toll_booth_cost number" name="toll_booth_cost[]" data="'.$v->bridge_cost_id.'" value="'.$this->lib->formatMoney($v->toll_booth_cost).'"><input '.$checked.' type="checkbox" class="check_vat" name="check_vat[]" value="1"> VAT</td></tr>';

                    $str .= '<tr><td>MST</td>';

                    $str .= '<td><input type="text" class="toll_mst" name="toll_mst[]" value="'.$v->toll_mst.'"></td>';

                    $str .= '<td>Mẫu số</td>';

                    $str .= '<td><input style="width:120px" type="text" class="toll_symbol" name="toll_symbol[]" value="'.$v->toll_symbol.'"></td></tr>';

                    $str .= '<tr><td>Loại</td>';

                    $str .= '<td><select style="width:200px" class="toll_type" name="toll_type[]"><option '.$type1.' value="1">Vé thu phí</option><option '.$type2.' value="2">Cước đường bộ</option></select></td>';

                    $str .= '</tr></table></td></tr>';

                }

            }



            echo $str;

        }

    }



    public function deletetollbooth(){

        if (isset($_POST['road'])) {

            $bridge_cost_model = $this->model->get('bridgecostModel');



            $bridge_cost_model->queryBridgecost('DELETE FROM bridge_cost WHERE bridge_cost_id = '.$_POST['bridge_cost_id'].' AND road = '.$_POST['road'].' AND toll_booth = '.$_POST['toll_booth']);

            echo 'Đã xóa thành công';

        }

    }



    public function distance(){

        if(isset($_POST['road'])){

            

            $distance_model = $this->model->get('distanceModel');

            $oil_model = $this->model->get('oilModel');
            $oils = $oil_model->getAllOil();
            

            $data = array(

                'where' => 'road = '.$_POST['road'],

            );

            $distances = $distance_model->getAllDistance($data);



            $str = "";

            if (!$distances) {

                $oil_data = "";
                foreach ($oils as $oil) {
                    $oil_data .= '<option data="'.$oil->oil.'" value="'.$oil->oil_id.'">'.$oil->way.'</option>';
                }

                $str .= '<tr class="'.$_POST['road'].'">';

                $str .= '<td><input type="checkbox"  name="chk2"></td>';

                $str .= '<td><table style="width: 100%">';

                $str .= '<tr class="'.$_POST['road'] .'">';

                $str .= '<td>Khoảng cách</td>';

                $str .= '<td><input style="width:90px" type="text" class="distance_km number" name="distance_km[]" >';

                $str .= '</td>';

                $str .= '<td>Định mức lit dầu</td>';

                $str .= '<td><input style="width:90px" type="text" disabled class="distance_oil" name="distance_oil[]" ></td></tr>';

                $str .= '<tr class="'.$_POST['road'] .'">';

                $str .= '<td>Chiều đi</td>';

                $str .= '<td colspan="2">';

                $str .= '<select style="width:90px" name="distance_way[]" class="distance_way" >';

                    $str .= $oil_data;

                $str .= '</select>';

                $str .= '</td>';

                $str .= '</tr></table></td></tr>';

            }

            else{

                foreach ($distances as $v) {

                    $oil_data = "";
                    foreach ($oils as $oil) {
                        if ($v->way == $oil->oil_id) {
                            $oil_data .= '<option selected data="'.$oil->oil.'" value="'.$oil->oil_id.'">'.$oil->way.'</option>';
                        }
                        else{
                            $oil_data .= '<option data="'.$oil->oil.'" value="'.$oil->oil_id.'">'.$oil->way.'</option>';
                        }
                        
                    }


                    $str .= '<tr class="'.$_POST['road'].'">';

                    $str .= '<td><input type="checkbox"  name="chk2" tabindex="'.$v->road.'" data="'.$v->km.'" title="'.$v->oil.'" alt="'.$v->way.'" ></td>';

                    $str .= '<td><table style="width: 100%">';

                    $str .= '<tr class="'.$_POST['road'] .'">';

                    $str .= '<td>Khoảng cách</td>';

                    $str .= '<td><input style="width:90px" type="text" class="distance_km number" name="distance_km[]" value="'.$v->km.'" >';

                    $str .= '</td>';

                    $str .= '<td>Định mức lit dầu</td>';

                    $str .= '<td><input style="width:90px" type="text" disabled class="distance_oil" name="distance_oil[]" value="'.$v->oil.'" ></td></tr>';

                    $str .= '<tr class="'.$_POST['road'] .'">';

                    $str .= '<td>Chiều đi</td>';

                    $str .= '<td colspan="2">';

                    $str .= '<select style="width:90px" name="distance_way[]" class="distance_way" >';

                        $str .= $oil_data;

                    $str .= '</select>';

                    $str .= '</td>';

                    $str .= '</tr></table></td></tr>';

                }

            }



            echo $str;

        }

    }



    public function deletedistance(){

        if (isset($_POST['road'])) {

            $distance_model = $this->model->get('distanceModel');



            $distance_model->queryDistance('DELETE FROM distance WHERE road = '.$_POST['road'].' AND way = '.$_POST['way'].' AND km = '.$_POST['km']);

            echo 'Đã xóa thành công';

        }

    }



    public function add(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->road) || json_decode($_SESSION['user_permission_action'])->road != "road") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $road = $this->model->get('roadModel');
            $road_temp = $this->model->get('roadtempModel');

            /**************/

            $toll_booth = $_POST['toll_booth'];

            /**************/

            $toll_booth_model = $this->model->get('tollModel');

            $bridge_cost_model = $this->model->get('bridgecostModel');



            $distance = $_POST['distance'];

            $distance_model = $this->model->get('distanceModel');

            $shipment = $this->model->get('shipmentModel');

            $data = array(
                        'road_from' => trim($_POST['road_from']),
                        'road_to' => trim($_POST['road_to']),
                        'route_from' => trim($_POST['route_from']),
                        'route_to' => trim($_POST['route_to']),

                        'road_oil' => trim($_POST['road_oil']),

                        'road_time' => trim($_POST['road_time']),

                        'road_km' => trim($_POST['road_km']),

                        'way' => trim($_POST['way']),

                        'bridge_cost' => trim(str_replace(',','',$_POST['bridge_cost'])),

                        'police_cost' => trim(str_replace(',','',$_POST['police_cost'])),

                        'tire_cost' => trim(str_replace(',','',$_POST['tire_cost'])),

                        'charge_add' => trim(str_replace(',','',$_POST['charge_add'])),

                        'road_add' => trim(str_replace(',','',$_POST['road_add'])),

                        'road_salary' => trim(str_replace(',','',$_POST['road_salary'])),

                        'road_oil_ton' => trim(str_replace(',','',$_POST['road_oil_ton'])),

                        'start_time' => strtotime(trim($_POST['start_time'])),

                        'end_time' => strtotime(trim($_POST['end_time'])),

                        'status' => trim($_POST['status']),

                        );

            if ($_POST['yes'] != "") {

                //$data['road_update_user'] = $_SESSION['userid_logined'];

                //$data['road_update_time'] = time();

                //var_dump($data);



                $road_d = $road->getRoad($_POST['yes']);

                $road1 = $road->getRoadByWhere(array('route_from'=>$road_d->route_from,'route_to'=>$road_d->route_to,'road_from'=>$road_d->road_from,'road_to'=>$road_d->road_to,'end_time'=>(strtotime(date('d-m-Y',strtotime(date('d-m-Y',$road_d->start_time).' -1 day'))))));
                $road2 = $road->getRoadByWhere(array('route_from'=>$road_d->route_from,'route_to'=>$road_d->route_to,'road_from'=>$road_d->road_from,'road_to'=>$road_d->road_to,'start_time'=>(strtotime(date('d-m-Y',strtotime(date('d-m-Y',$road_d->end_time).' +1 day'))))));
                if($road1)
                    $road->updateRoad(array('route_from'=>$road_d->route_from,'route_to'=>$road_d->route_to,'road_from'=>$road_d->road_from,'road_to'=>$road_d->road_to,'end_time'=>(strtotime(date('d-m-Y',strtotime($_POST['start_time'].' -1 day'))))),array('road_id' => $road1->road_id));
                if($road2)
                    $road->updateRoad(array('route_from'=>$road_d->route_from,'route_to'=>$road_d->route_to,'road_from'=>$road_d->road_from,'road_to'=>$road_d->road_to,'start_time'=>(strtotime(date('d-m-Y',strtotime($_POST['end_time'].' +1 day'))))),array('road_id' => $road2->road_id));


                $route_from = $road_d->route_from;

                $route_to = $road_d->route_to;

                $warehouse_from = $road_d->road_from;

                $warehouse_to = $road_d->road_to;

                $road_time_old = $road_d->road_time;

                $start_time_old = $road_d->start_time;

                $end_time_old = $road_d->end_time;

                $police_cost_old = $road_d->police_cost;

                $bridge_cost_old = $road_d->bridge_cost;

                $tire_cost_old = $road_d->tire_cost;

                $road_oil_old = $road_d->road_oil;

                $charge_add_old = $road_d->charge_add;



                



                $shipments = $shipment->getAllShipment(array('where'=>'shipment_from = '.$warehouse_from.' AND shipment_to = '.$warehouse_to.' AND shipment_date >= '.$data['start_time'].' AND shipment_date <= '.$data['end_time']));

                if($shipments){
                    foreach ($shipments as $ship) {

                        $data_edit = array(

                            'shipment_cost' => ($ship->shipment_cost-($police_cost_old+round($bridge_cost_old*1.1)+$tire_cost_old+($road_oil_old*round($ship->oil_cost*1.1))))+($data['police_cost']+round($data['bridge_cost']*1.1)+$data['tire_cost']+($data['road_oil']*round($ship->oil_cost*1.1))),

                            'shipment_bonus' => ($ship->shipment_ton>29000)?round($ship->shipment_ton-29000)*$data['charge_add']:0,

                            );

                        $shipment->updateShipment($data_edit,array('shipment_id' => $ship->shipment_id));

                    }
                }

                

                $road->updateRoad($data,array('road_id' => $_POST['yes']));

                echo "Cập nhật thành công";

                $data2 = array('road_id'=>$_POST['yes'],'road_temp_date'=>strtotime(date('d-m-Y')),'road_temp_action'=>2,'road_temp_user'=>$_SESSION['userid_logined'],'name'=>'Tuyến đường');
                $data_temp = array_merge($data, $data2);
                $road_temp->createRoad($data_temp);



                foreach ($toll_booth as $v) {

                    if (isset($v['toll_booth_id']) && $v['toll_booth_id'] != "") {

                        $id_toll_booth = $v['toll_booth_id'];

                        $data_toll_booth = array(

                            'toll_name' => $v['toll_booth_name'],

                            'toll_mst' => $v['toll_mst'],

                            'toll_symbol' => $v['toll_symbol'],

                            'toll_type' => $v['toll_type'],

                        );

                        $toll_booth_model->updateToll($data_toll_booth,array('toll_id'=>$id_toll_booth));

                    }

                    else{

                        if (trim($v['toll_booth_name']) != "") {
                            $data_toll_booth = array(

                                'toll_name' => $v['toll_booth_name'],

                                'toll_mst' => $v['toll_mst'],

                                'toll_symbol' => $v['toll_symbol'],

                                'toll_type' => $v['toll_type'],

                            );

                            if (!$toll_booth_model->getTollByWhere(array('toll_name'=>$data_toll_booth['toll_name']))) {
                                $toll_booth_model->createToll($data_toll_booth);

                                $id_toll_booth = $toll_booth_model->getLastToll()->toll_id;
                            }
                            else{
                                $id_toll_booth = $toll_booth_model->getTollByWhere(array('toll_name'=>$data_toll_booth['toll_name']))->toll_id;
                                
                            }
                            
                        }

                        

                    }

                    if (isset($id_toll_booth)) {
                        $data_bridge_cost = array(

                            'toll_booth' => $id_toll_booth,

                            'road' => $_POST['yes'],

                            'toll_booth_cost' => trim(str_replace(',','',$v['toll_booth_cost'])),
                            'check_vat' => $v['check_vat'],

                        );

                        $id_bridgecost = isset($v['bridge_cost_id'])?$v['bridge_cost_id']:0;

                        if ($id_bridgecost > 0) {
                            $bridge_cost_model->updateBridgecost($data_bridge_cost,array('bridge_cost_id'=>$id_bridgecost));
                        }
                        else{
                            $bridge_cost_model->createBridgecost($data_bridge_cost);
                        }
                    }

                    


                }



                foreach ($distance as $v) {

                    

                    $data_distance = array(

                        'road' => $_POST['yes'],

                        'km' => trim(str_replace(',','',$v['km'])),

                        'oil' => $v['oil'],

                        'way' => $v['way'],

                    );

                    if (!$distance_model->getDistanceByWhere(array('road'=>$_POST['yes'],'way'=>$data_distance['way']))) {

                        $distance_model->createDistance($data_distance);

                    }

                    else if ($distance_model->getDistanceByWhere(array('road'=>$_POST['yes'],'way'=>$data_distance['way']))) {

                        $id_distance = $distance_model->getDistanceByWhere(array('road'=>$_POST['yes'],'way'=>$data_distance['way']))->distance_id;

                        

                        $distance_model->updateDistance($data_distance,array('distance_id'=>$id_distance));

                    }

                }





                date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|road|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

            }

            else{

                //$data['road_create_user'] = $_SESSION['userid_logined'];

                //$data['staff'] = $_POST['staff'];

                //var_dump($data);

                

                if ($road->getRoadByWhere(array('road_from'=>$_POST['road_from'],'road_to'=>$_POST['road_to'],'start_time'=>$_POST['start_time'],'end_time'=>$_POST['end_time']))) {

                    echo "Bảng định mức này đã tồn tại";

                    return false;

                }

                else{

                    $dm1 = $road->queryRoad('SELECT * FROM road WHERE route_from='.$data['route_from'].' AND route_to='.$data['route_to'].' AND road_from='.$data['road_from'].' AND road_to='.$data['road_to'].' AND start_time <= '.$data['start_time'].' AND end_time <= '.$data['end_time'].' AND end_time >= '.$data['start_time'].' ORDER BY end_time ASC LIMIT 1');
                    $dm2 = $road->queryRoad('SELECT * FROM road WHERE route_from='.$data['route_from'].' AND route_to='.$data['route_to'].' AND road_from='.$data['road_from'].' AND road_to='.$data['road_to'].' AND end_time >= '.$data['end_time'].' AND start_time >= '.$data['start_time'].' AND start_time <= '.$data['end_time'].' ORDER BY end_time ASC LIMIT 1');
                    $dm3 = $road->queryRoad('SELECT * FROM road WHERE route_from='.$data['route_from'].' AND route_to='.$data['route_to'].' AND road_from='.$data['road_from'].' AND road_to='.$data['road_to'].' AND start_time <= '.$data['start_time'].' AND end_time >= '.$data['end_time'].' ORDER BY end_time ASC LIMIT 1');

                    if ($dm3) {
                            foreach ($dm3 as $row) {
                                $d = array(
                                    'end_time' => strtotime(date('d-m-Y',strtotime($_POST['start_time'].' -1 day'))),
                                    );
                                $road->updateRoad($d,array('road_id'=>$row->road_id));

                                $c = array(
                                    'route_from' => $row->route_from,
                                    'route_to' => $row->route_to,
                                    'road_from' => $row->road_from,
                                    'road_to' => $row->road_to,
                                    'road_oil' => $row->road_oil,
                                    'road_time' => $row->road_time,
                                    'road_km' => $row->road_km,
                                    'way' => $row->way,
                                    'bridge_cost' => $row->bridge_cost,
                                    'police_cost' => $row->police_cost,
                                    'tire_cost' => $row->tire_cost,
                                    'charge_add' => $row->charge_add,
                                    'road_add' => $row->road_add,
                                    'road_oil_ton' => $row->road_oil_ton,
                                    'road_salary' => $row->road_salary,
                                    'start_time' => strtotime(date('d-m-Y',strtotime($_POST['end_time'].' +1 day'))),
                                    'end_time' => $row->end_time,
                                    'status' => $row->status
                                    );
                                $road->createRoad($c);

                                $road_id = $road->getLastRoad()->road_id;

                                $bridgecost = $bridge_cost_model->getAllBridgecost(array('where'=>'road = '.$row->road_id));
                                foreach ($bridgecost as $bridge) {
                                    $data_bridge_cost = array(

                                        'toll_booth' => $bridge->toll_booth,

                                        'road' => $road_id,

                                        'toll_booth_cost' => $bridge->toll_booth_cost,

                                        'check_vat' => $bridge->check_vat,

                                    );

                                    $bridge_cost_model->createBridgecost($data_bridge_cost);
                                }

                                $distances = $distance_model->getAllDistance(array('where'=>'road = '.$row->road_id));
                                foreach ($distances as $v) {

                                    $data_distance = array(

                                        'road' => $road_id,

                                        'km' => $v->km,

                                        'oil' => $v->oil,

                                        'way' => $v->way,

                                    );

                                     $distance_model->createDistance($data_distance);
                                }

                            }

                            

                            
                            $road->createRoad($data);

                        }
                        else if ($dm1 || $dm2) {
                            if($dm1){
                                foreach ($dm1 as $row) {
                                    $d = array(
                                        'end_time' => strtotime(date('d-m-Y',strtotime($_POST['start_time'].' -1 day'))),
                                        );
                                    $road->updateRoad($d,array('road_id'=>$row->road_id));

                                    
                                }
                            }
                            if($dm2){
                                foreach ($dm2 as $row) {
                                    $d = array(
                                        'start_time' => strtotime(date('d-m-Y',strtotime($_POST['end_time'].' +1 day'))),
                                        );
                                    $road->updateRoad($d,array('road_id'=>$row->road_id));


                                }
                            }


                            
                            $road->createRoad($data);

                        
                    }
                    else{
                        $road->createRoad($data);

                    }


                    $shipments = $shipment->getAllShipment(array('where'=>'shipment_from = '.$data['road_from'].' AND shipment_to = '.$data['road_to'].' AND shipment_date >= '.$data['start_time'].' AND shipment_date <= '.$data['end_time']));

                    if($shipments){
                        foreach ($shipments as $ship) {

                            $data_edit = array(

                                'shipment_cost' => $ship->shipment_cost+($data['police_cost']+round($data['bridge_cost']*1.1)+$data['tire_cost']+($data['road_oil']*round($ship->oil_cost*1.1))),

                                'shipment_bonus' => ($ship->shipment_ton>29000)?round($ship->shipment_ton-29000)*$data['charge_add']:0,

                                );

                            $shipment->updateShipment($data_edit,array('shipment_id' => $ship->shipment_id));

                        }
                    }


                    $data2 = array('road_id'=>$road->getLastRoad()->road_id,'road_temp_date'=>strtotime(date('d-m-Y')),'road_temp_action'=>1,'road_temp_user'=>$_SESSION['userid_logined'],'name'=>'Tuyến đường');
                    $data_temp = array_merge($data, $data2);
                    $road_temp->createRoad($data_temp);

                    $road_id = $road->getLastRoad()->road_id;



                    foreach ($toll_booth as $v) {

                        if (isset($v['toll_booth_id']) && $v['toll_booth_id'] != "") {

                            $id_toll_booth = $v['toll_booth_id'];

                            $data_toll_booth = array(

                                'toll_name' => $v['toll_booth_name'],

                                'toll_mst' => $v['toll_mst'],

                                'toll_symbol' => $v['toll_symbol'],

                                'toll_type' => $v['toll_type'],

                            );

                            $toll_booth_model->updateToll($data_toll_booth,array('toll_id'=>$id_toll_booth));

                        }

                        else{

                            if (trim($v['toll_booth_name']) != "") {
                                $data_toll_booth = array(

                                    'toll_name' => $v['toll_booth_name'],

                                    'toll_mst' => $v['toll_mst'],

                                    'toll_symbol' => $v['toll_symbol'],

                                    'toll_type' => $v['toll_type'],

                                );

                                if (!$toll_booth_model->getTollByWhere(array('toll_name'=>$data_toll_booth['toll_name']))) {
                                    $toll_booth_model->createToll($data_toll_booth);

                                    $id_toll_booth = $toll_booth_model->getLastToll()->toll_id;
                                }
                                else{
                                    $id_toll_booth = $toll_booth_model->getTollByWhere(array('toll_name'=>$data_toll_booth['toll_name']))->toll_id;
                                    
                                }

                                
                            }

                            

                        }

                        if (isset($id_toll_booth)) {
                            $data_bridge_cost = array(

                                'toll_booth' => $id_toll_booth,

                                'road' => $road_id,

                                'toll_booth_cost' => trim(str_replace(',','',$v['toll_booth_cost'])),

                                'check_vat' => $v['check_vat'],

                            );



                            $bridge_cost_model->createBridgecost($data_bridge_cost);
                        }

                        

                       
                    }



                    foreach ($distance as $v) {

                    

                    $data_distance = array(

                        'road' => $road_id,

                        'km' => trim(str_replace(',','',$v['km'])),

                        'oil' => $v['oil'],

                        'way' => $v['way'],

                    );

                     $distance_model->createDistance($data_distance);

                    
                    echo "Thêm thành công";

                }





                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$road->getLastRoad()->road_id."|road|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                

            }

                    

        }

    }



    public function getroadfrom(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

       

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $place_model = $this->model->get('placeModel');

            

            if ($_POST['keyword'] == "*") {

                $list = $place_model->getAllPlace();

            }

            else{

                $data = array(

                'where'=>'( place_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $place_model->getAllPlace($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $place_name = $rs->place_name;

                if ($_POST['keyword'] != "*") {

                    $place_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->place_name);

                }

                

                // add new option

                echo '<li onclick="set_item_road_from(\''.$rs->place_id.'\',\''.$rs->place_name.'\')">'.$place_name.'</li>';

            }

        }

    }



    public function getroadto(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $place_model = $this->model->get('placeModel');

            

            if ($_POST['keyword'] == "*") {


                $list = $place_model->getAllPlace();

            }

            else{

                $data = array(

                'where'=>'( place_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $place_model->getAllPlace($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $place_name = $rs->place_name;

                if ($_POST['keyword'] != "*") {

                    $place_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->place_name);

                }

                

                // add new option

                echo '<li onclick="set_item_road_to(\''.$rs->place_id.'\',\''.$rs->place_name.'\')">'.$place_name.'</li>';

            }

        }

    }

    public function getroutefrom(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $place_model = $this->model->get('routeModel');

            

            if ($_POST['keyword'] == "*") {

                $list = $place_model->getAllPlace();

            }

            else{

                $data = array(

                'where'=>'( route_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $place_model->getAllPlace($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $place_name = $rs->route_name;

                if ($_POST['keyword'] != "*") {

                    $place_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->route_name);

                }

                

                // add new option

                echo '<li onclick="set_item_route_from(\''.$rs->route_id.'\',\''.$rs->route_name.'\')">'.$place_name.'</li>';

            }

        }

    }



    public function getrouteto(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $place_model = $this->model->get('routeModel');

            

            if ($_POST['keyword'] == "*") {


                $list = $place_model->getAllPlace();

            }

            else{

                $data = array(

                'where'=>'( route_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $place_model->getAllPlace($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $place_name = $rs->route_name;

                if ($_POST['keyword'] != "*") {

                    $place_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->route_name);

                }

                

                // add new option

                echo '<li onclick="set_item_route_to(\''.$rs->route_id.'\',\''.$rs->route_name.'\')">'.$place_name.'</li>';

            }

        }

    }



    public function gettollbooth(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $tollbooth_model = $this->model->get('tollModel');
            $bridgecost_model = $this->model->get('bridgecostModel');

            

            if ($_POST['keyword'] == "*") {

                

                $list = $tollbooth_model->getAllTollbooth();

            }

            else{

                $data = array(

                'where'=>'( toll_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $tollbooth_model->getAllToll($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text
                

                $tollbooth_name = $rs->toll_name;

                if ($_POST['keyword'] != "*") {

                    $tollbooth_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->toll_name);

                }

                $bridge = $bridgecost_model->getBridgecostByWhere(array('toll_booth'=>$rs->toll_id));
                $bridges = $bridgecost_model->queryBridgecost('SELECT * FROM bridge_cost WHERE toll_booth = '.$rs->toll_id.' ORDER BY bridge_cost_id DESC LIMIT 1');
                if ($bridges) {
                    foreach ($bridges as $bridge) {
                        echo '<li onclick="set_item_other(\''.$rs->toll_id.'\',\''.$rs->toll_name.'\',\''.$rs->toll_mst.'\',\''.$rs->toll_type.'\',\''.$rs->toll_symbol.'\',\''.$_POST['offset'].'\',\''.$bridge->toll_booth_cost.'\',\''.$bridge->check_vat.'\')">'.$tollbooth_name.'</li>';
                    }
                    
                }
                else{
                    echo '<li onclick="set_item_other(\''.$rs->toll_id.'\',\''.$rs->toll_name.'\',\''.$rs->toll_mst.'\',\''.$rs->toll_type.'\',\''.$rs->toll_symbol.'\',\''.$_POST['offset'].'\',0,0)">'.$tollbooth_name.'</li>';
                }

                // add new option

                

            }

        }

    }

    



    public function delete(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->road) || json_decode($_SESSION['user_permission_action'])->road != "road") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $road = $this->model->get('roadModel');
            $road_temp = $this->model->get('roadtempModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {

                     $road_data = (array)$road->getRoad($data); 

                    $road->deleteRoad($data);                   
                    
                    $data2 = array('road_id'=>$data,'road_temp_date'=>strtotime(date('d-m-Y')),'road_temp_action'=>3,'road_temp_user'=>$_SESSION['userid_logined'],'name'=>'Tuyến đường');
                    $data_temp = array_merge($road_data, $data2);
                    $road_temp->createRoad($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|road|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                return true;

            }

            else{

                $road_data = (array)$road->getRoad($_POST['data']);
                $data2 = array('road_id'=>$_POST['data'],'road_temp_date'=>strtotime(date('d-m-Y')),'road_temp_action'=>3,'road_temp_user'=>$_SESSION['userid_logined'],'name'=>'Tuyến đường');
                    $data_temp = array_merge($road_data, $data2);
                    $road_temp->createRoad($data_temp);

                date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|road|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $road->deleteRoad($_POST['data']);

            }

            

        }

    }



    public function import(){

        $this->view->disableLayout();

        header('Content-Type: text/html; charset=utf-8');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->road) || json_decode($_SESSION['user_permission_action'])->road != "road") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {



            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $road = $this->model->get('roadModel');

            $warehouse = $this->model->get('warehouseModel');



            $objPHPExcel = new PHPExcel();

            // Set properties

            if (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == "xls") {

                $objReader = PHPExcel_IOFactory::createReader('Excel5');

            }

            else if (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == "xlsx") {

                $objReader = PHPExcel_IOFactory::createReader('Excel2007');

            }

            

            $objReader->setReadDataOnly(false);



            $objPHPExcel = $objReader->load($_FILES['import']['tmp_name']);

            $objWorksheet = $objPHPExcel->getActiveSheet();



            



            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10

            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'



            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5



            //var_dump($objWorksheet->getMergeCells());die();

            

             



                for ($row = 3; $row <= $highestRow; ++ $row) {

                    $val = array();

                    for ($col = 0; $col < $highestColumnIndex; ++ $col) {

                        $cell = $objWorksheet->getCellByColumnAndRow($col, $row);

                        // Check if cell is merged

                        foreach ($objWorksheet->getMergeCells() as $cells) {

                            if ($cell->isInRange($cells)) {

                                $currMergedCellsArray = PHPExcel_Cell::splitRange($cells);

                                $cell = $objWorksheet->getCell($currMergedCellsArray[0][0]);

                                break;

                                

                            }

                        }

                        //$val[] = $cell->getValue();

                        $val[] = is_numeric($cell->getCalculatedValue()) ? round($cell->getCalculatedValue()) : $cell->getCalculatedValue();

                        //here's my prob..

                        //echo $val;

                    }

                    if ($val[1] != null && $val[2] != null && $val[3] != null && $val[4] != null && $val[5] != null) {



                            if($warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[1])))) {

                                $id_from = $warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[1])))->warehouse_id;

                            }

                            else if(!$warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[1])))){

                                $warehouse_data_from = array(

                                    'warehouse_name' => trim($val[1]),

                                    );

                                $warehouse->createWarehouse($warehouse_data_from);



                                $id_from = $warehouse->getLastWarehouse()->warehouse_id;

                            }



                            if($warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[2])))) {

                                $id_to = $warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[2])))->warehouse_id;

                            }

                            else if(!$warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[2])))){

                                $warehouse_data_to = array(

                                    'warehouse_name' => trim($val[2]),

                                    );

                                $warehouse->createWarehouse($warehouse_data_to);



                                $id_to = $warehouse->getLastWarehouse()->warehouse_id;

                            }



                            if ($id_from != null && $id_to != null) {

                                $chieu = (trim($val[3])=="Lên")?1:((trim($val[3])=="Xuống")?2:((trim($val[3])=="Bằng")?3:((trim($val[3])=="ĐN-QN")?4:0)));

                                if($road->getRoadByWhere(array('road_from'=>$id_from,'road_to'=>$id_to))) {

                                    $id_road = $road->getRoadByWhere(array('road_from'=>$id_from,'road_to'=>$id_to))->road_id;



                                    $road_data = array(

                                    'way' => $chieu,

                                    'road_km' => trim($val[4]),

                                    'road_oil' => trim($val[5]),

                                    'bridge_cost' => round(trim($val[6])/1.1),

                                    'police_cost' => trim($val[7]),

                                    'tire_cost' => trim($val[8]),

                                    'charge_add' => trim($val[9]),

                                    'road_time' => trim($val[10]),

                                    );

                                    $road->updateRoad($road_data,array('road_id' => $id_road));

                                }

                                else{

                                    $road_data = array(

                                    'road_from' => $id_from,

                                    'road_to' => $id_to,

                                    'way' => $chieu,

                                    'road_km' => trim($val[4]),

                                    'road_oil' => trim($val[5]),

                                    'bridge_cost' => round(trim($val[6])/1.1),

                                    'police_cost' => trim($val[7]),

                                    'tire_cost' => trim($val[8]),

                                    'charge_add' => trim($val[9]),

                                    'road_time' => trim($val[10]),

                                    );

                                    $road->createRoad($road_data);

                                }

                            }

                        

                    }

                    

                    //var_dump($this->getNameDistrict($this->lib->stripUnicode($val[1])));

                    // insert





                }

                //return $this->view->redirect('transport');

            

            return $this->view->redirect('road');

        }

        $this->view->show('road/import');



    }

    



    public function view() {

        

        $this->view->show('handling/view');

    }

    function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        

            $warehouse_model = $this->model->get('warehouseModel');

            $road_model = $this->model->get('roadModel');

            

            $road = $road_model->getAllRoad();





            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

               ->setCellValue('A1', 'STT')

               ->setCellValue('B1', 'Kho đi')

               ->setCellValue('C1', 'Kho đến')

               ->setCellValue('D1', 'Chiều đi')

               ->setCellValue('E1', 'Khoảng cách')

               ->setCellValue('F1', 'Lit dầu')

               ->setCellValue('G1', 'Cầu đường')

               ->setCellValue('H1', 'Công an')

               ->setCellValue('I1', 'Vá vỏ')

               ->setCellValue('J1', 'Cước vượt tải')

               ->setCellValue('K1', 'Bồi dưỡng kho đi')

               ->setCellValue('L1', 'Bồi dưỡng kho đến')

               ->setCellValue('M1', 'Cân xe')

               ->setCellValue('N1', 'Quét cont')

               ->setCellValue('O1', 'Vé cổng')

               ->setCellValue('P1', 'Ngày áp dụng')

               ->setCellValue('Q1', 'Ngày hết hạn');

               



            



            

            

            



            if ($road) {



                $hang = 2;

                $i=1;



                $kho_data = array();

                foreach ($road as $row) {

                    $tongboiduongdi = 0;

                    $tongboiduongden = 0;

                    $canxe = 0;

                    $quetcont = 0;

                    $vecong = 0;

                    $khodi = $warehouse_model->getAllWarehouse(array('where'=> 'warehouse_id = '.$row->road_from)); 

                    foreach ($khodi as $ware) {

                        $kho_data[$ware->warehouse_id] = $ware->warehouse_name;

                        $tongboiduongdi += $ware->warehouse_add + $ware->warehouse_ton;

                        $canxe += $ware->warehouse_weight;

                        $quetcont += $ware->warehouse_clean;

                        $vecong += $ware->warehouse_gate;

                    }



                    $khoden = $warehouse_model->getAllWarehouse(array('where'=> 'warehouse_id = '.$row->road_to)); 

                    foreach ($khoden as $ware2) {

                        $kho_data[$ware2->warehouse_id] = $ware2->warehouse_name;

                        $tongboiduongden += $ware2->warehouse_add + $ware2->warehouse_ton;

                        $canxe += $ware2->warehouse_weight;

                        $quetcont += $ware2->warehouse_clean;

                        $vecong += $ware2->warehouse_gate;

                    }





                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                     $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('A' . $hang, $i++)

                        ->setCellValueExplicit('B' . $hang, $kho_data[$row->road_from])

                        ->setCellValue('C' . $hang, $kho_data[$row->road_to])

                        ->setCellValue('D' . $hang, $row->way==0?"Rỗng":($row->way==1?"Lên":($row->way==2?"Xuống":($row->way==3?"Bằng":"ĐN-QN"))))

                        ->setCellValue('E' . $hang, $row->road_km)

                        ->setCellValue('F' . $hang, $row->road_oil)

                        ->setCellValue('G' . $hang, $row->bridge_cost)

                        ->setCellValue('H' . $hang, $row->police_cost)

                        ->setCellValue('I' . $hang, $row->tire_cost)

                        ->setCellValue('J' . $hang, $row->charge_add)

                        ->setCellValue('K' . $hang, $tongboiduongdi)

                        ->setCellValue('L' . $hang, $tongboiduongden)

                        ->setCellValue('M' . $hang, $canxe)

                        ->setCellValue('N' . $hang, $quetcont)

                        ->setCellValue('O' . $hang, $vecong)

                        ->setCellValue('P' . $hang, $this->lib->hien_thi_ngay_thang($row->start_time))

                        ->setCellValue('Q' . $hang, $this->lib->hien_thi_ngay_thang($row->end_time));

                     $hang++;





                  }



          }



            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;





            $objPHPExcel->getActiveSheet()->getStyle('E2:O'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A1:O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:O1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(16);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);



            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Road Report")

                            ->setSubject("Road Report")

                            ->setDescription("Road Report.")

                            ->setKeywords("Road Report")

                            ->setCategory("Road Report");

            $objPHPExcel->getActiveSheet()->setTitle("Bang dinh muc");



            $objPHPExcel->getActiveSheet()->freezePane('A2');

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG ĐỊNH MỨC.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }



}

?>