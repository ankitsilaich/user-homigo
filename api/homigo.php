<?php

require_once 'NotORM.php';

$connection = new PDO('mysql:dbname=homig7y7_main;host=localhost', 'homig7y7_main', 'homigo10450');

$db = new NotORM($connection);

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
session_start();

$authenticate = function($app)
{
    return function() use ($app)
    {
        if (!isset($_SESSION['user'])) {
            
            
            $app->redirect('/login');
        }
    };
};
$app->post("/auth/process/admin", function() use ($app, $db)
{
    $array    = (array) json_decode($app->request()->getBody());
    $email    = $array['email'];
    $password = $array['password'];
    $person   = $db->admin()->where('email', $email)->where('password', $password);
    $count    = count($person);
    
    if ($count == 1) {
        
        $_SESSION['admin'] = $email;
        $data             = array(
            'login_success' => "true",
            'login_attempt_by' => $email,
            'message' => "Successfull sigin"
            
        );
        
    } else {
        $data = array(
            'login_success' => "false",
            'login_attempt_by' => $email,
            'message' => "please provide correct details"
            
        );
        
        
        
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
    
    
    
});
$app->get('/auth/process/admin', function() use ($app)
{
    
    if (isset($_SESSION['admin'])) {
        $data = $_SESSION['admin'];
    } else {
        $data = false;
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
});

$app->get("/auth/logout/admin", function() use ($app)
{
    unset($_SESSION['admin']);
    
    
});
$app->post("/auth/process/user", function() use ($app, $db)
{
    $array    = (array) json_decode($app->request()->getBody());
    $email    = $array['email'];
    $password = $array['password'];
    $person   = $db->tenants()->where('email', $email)->where('password', $password);
    $count    = count($person);
    
    if ($count == 1) {
       $p = $person->fetch();
       if($p['isVerified'] == '0'){
       $data             = array(
            'login_success' => "false",
            'login_attempt_by' => $email,
            'message' => "You are not Verified please wait for Homigo Verification."
            
        );

       }else{
        $_SESSION['user'] = $email;
        $_SESSION['id'] = $p['id'];
        $data             = array(
            'login_success' => "true",
            'login_attempt_by' => $email,
            'message' => "Successfull sigin"
            
        );}
        
    } else {
        $data = array(
            'login_success' => "false",
            'login_attempt_by' => $email,
            'message' => "please provide correct details"
            
        );
        
        
        
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
    
    
    
});
$app->get('/auth/process/user', function() use ($app)
{
    
    if (isset($_SESSION['user'])) {
        $data = $_SESSION['user'];
    } else {
        $data = false;
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
});

$app->get("/auth/logout/user", function() use ($app)
{
    unset($_SESSION['user']);
    unset($_SESSION['id']);
    
    
});
$app->post("/doupload", function() use ($app,$db)
{
    if ($houses = $db->tenants()->select('id')->order('id desc')->limit(1,0)->fetch()) {
     //echo $houses;
    }
    $houses = $houses['id'] + 1;
    if ( !empty( $_FILES ) ) {

    $tempPath = $_FILES[ 'file' ][ 'tmp_name' ];
    $temp = explode(".",$_FILES["file"]["name"]);
  $newfilename = $houses. '.' .'jpg';
    $uploadPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR .$newfilename;
    
    move_uploaded_file( $tempPath, $uploadPath );

    $answer = array( 'lastid' => $houses,
    'filename' => $newfilename);
    $json = json_encode( $answer );

    echo $json;

   } else {

    echo 'No files';

   }

    
});

//Get Method to get the data from database
function days($givendate)
{
    $now       = time(); // or your date as well
    $your_date = strtotime($givendate);
    $datediff  = $your_date - $now;
    return floor($datediff / (60 * 60 * 24));
    
}
;

$app->get('/houses(/:id)', function($id = null) use ($app, $db)
{
    
    if ($id == null) {
        $data  = array();
        $count = 0;
        foreach ($db->houses() as $houses) {
            
            $houses_tenants = array();
            $houses_deposits = array();
         
            foreach ($houses->houses_tenants() as $p) {
                $count++;
                $houses_tenants[] = array(
                    'id' => $p->tenants['id'],
                    'name' => $p->tenants["name"],
                    'phone' => $p->tenants["phone"]
                );
            }
             foreach ($houses->houses_deposits() as $p) {
                
                $houses_deposits[] = array(
                    'id' => $p->deposits['id'],
                    'rent' => $p->deposits["rent"],
                    'date' => $p->deposits["date"],
                     'status' => $p->deposits["status"]
                );
            }
            $data[] = array(
                'house_id' => $houses['id'],
                'house_name' => $houses['name'],
                'house_address' => $houses['address'],
                'house_entry_date' => $houses['entry_date'],
                'house_fixed_date' => $houses['due_date'],
                'house_rent' => $houses['rent'],
                'house_no_of_rooms' => $houses['totalrooms'],
                'house_totaldeposit' => $houses['totaldeposit'],
                'house_totaldepositleft' => $houses['depositleft'],
                'house_dthbill' => $houses['dthbill'],
                'house_dthbilldate' => $houses['dthbilldate'],
                'house_dthbilldays' => days($houses['dthbilldate']),
                'house_powerbill' => $houses['powerbill'],
                'house_powerbilldate' => $houses['powerbilldate'],
                'house_powerbilldays' => days($houses['powerbilldate']),
                'house_wifibill' => $houses['wifibill'],
                'house_wifibilldate' => $houses['wifibilldate'],
                'house_wifibilldays' => days($houses['wifibilldate']),
                'house_owner' => array(
                    'name' => $houses->owners['owner_name'],
                    'address' => $houses->owners['owner_address'],
                    'phone' => $houses->owners['owner_phone'],
                    'email' => $houses->owners['owner_email']
                    
                ),
                'tenants' => $houses_tenants,
                'deposits' => $houses_deposits,
                
                
            );
        }
    } else {
        
        $data = null;
        
        if ($houses = $db->houses()->where('id', $id)->fetch()) {
          
            $houses_tenants = array();
            $houses_deposits = array();
            foreach ($houses->houses_tenants() as $p) {
                
                $houses_tenants[] = array(
                    'id' => $p->tenants['id'],
                    'name' => $p->tenants["name"],
                    'phone' => $p->tenants["phone"]
                );
            }
            foreach ($houses->houses_deposits() as $p) {
                
                $houses_deposits[] = array(
                    'id' => $p->deposits['id'],
                    'rent' => $p->deposits["rent"],
                    'date' => $p->deposits["date"],
                     'status' => $p->deposits["status"]
                );
            }
            $data = array(
                'house_id' => $houses['id'],
                'house_name' => $houses['name'],
                'house_address' => $houses['address'],
                'house_fixed_date' => $houses['due_date'],
                'house_entry_date' => $houses['entry_date'],
                'house_rent' => $houses['rent'],
                'house_no_of_rooms' => $houses['totalrooms'],
                'house_totaldeposit' => $houses['totaldeposit'],
                'house_totaldepositleft' => $houses['depositleft'],
                'house_dthbill' => $houses['dthbill'],
                'house_dthbilldate' => $houses['dthbilldate'],
                'house_dthbilldays' => days($houses['dthbilldate']),
                'house_powerbill' => $houses['powerbill'],
                'house_powerbilldate' => $houses['powerbilldate'],
                
                'house_powerbilldays' => days($houses['powerbilldate']),
                'house_wifibill' => $houses['wifibill'],
                'house_wifibilldate' => $houses['wifibilldate'],
                'house_wifibilldays' => days($houses['wifibilldate']),
                'house_owner' => array(
                    'name' => $houses->owners['owner_name'],
                    'address' => $houses->owners['owner_address'],
                    'phone' => $houses->owners['owner_phone'],
                    'email' => $houses->owners['owner_email']
                ),
                'tenants' => $houses_tenants,
                'deposits' => $houses_deposits,
               
            );
        }
    }
    $houses = array(
        'aaData' => $data
    );
    $app->response()->header('content-type', 'application/json');
    
    echo json_encode($houses);
});

$app->get('/tenants', function($id = null) use ($app, $db)

{$data  = array();
    $tenants_deposits = array();
     if (isset($_SESSION['id'])) {
    
    
        
        $count = 0;
        foreach ($db->tenants()->where('id', $_SESSION['id']) as $tenants) {
            
            foreach ($tenants->tenants_deposits() as $p) {
                
                $tenants_deposits[] = array(
                    'id' => $p->deposits['id'],
                    'rent' => $p->deposits["rent"],
                    'date' => $p->deposits["date"],
                    'status' => $p->deposits["status"]
                );
            }
            $days   = days($tenants['entry_date']);
         
            $data[] = array(
                'id' => $tenants['id'],
                'address' => $tenants['address'],
                'name' => $tenants['name'],
                'email' => $tenants['email'],
                'phone' => $tenants['phone'],
                'company' => $tenants['company'],
                'rent' => $tenants['rent'],
                'totaldeposit' => $tenants['totaldeposit'],
                'depositleft' => $tenants['depositleft'],
                'entry_date' => $tenants['entry_date'],
                'deposits' => $tenants_deposits,
                'rentfirst' => $tenants['rentfirst'],
                 'is_Paid' => $tenants['isPaid']
                
                // 'house_totaldeposit' => $houses['house_total_deposit'],
                // 'house_totaldepositleft' => $houses['house_deposit_left'],
                // 'house_dthbill' => $houses['house_dth_bill_amount'],
                // 'house_dthbilldays' => days($houses['house_dth_bill_date']),
                // 'house_owner' => array('name' =>   $houses->owners['owner_name'],
                //                             'address' => $houses->owners['owner_address']
                //                              ),
                // 'tenants' => $houses_tenants,
                // 'days' => $days
                
            );
    }}
    $tenants = array(
        'aaData' => $data
    );
    $app->response()->header('content-type', 'application/json');
    
    echo json_encode($tenants);
});
$app->get('/tenants/search/:id', function($id = null) use ($app, $db)
{
    
    $data  = array();
    $count = 0;
    foreach ($db->tenants()->where("name LIKE ?", "%" . $id . "%") as $tenants) {
        
        
        $days   = days($tenants['entry_date']);
        //  $dthdays = days($houses['house_rent_due_date']);
        //  foreach ($houses->houses_tenants() as $p) {
        //      $count++;
        //         $houses_tenants[] = array('name'=>$p->tenants["name"],
        //          'phone'=>$p->tenants["phone"]);
        // }
        $data[] = array(
            'id' => $tenants['id'],
            'address' => $tenants['address'],
            'name' => $tenants['name'],
            'phone' => $tenants['phone'],
            'company' => $tenants['company'],
            'rent' => $tenants['rent'],
            'totaldeposit' => $tenants['totaldeposit'],
            'depositleft' => $tenants['depositleft'],
            'entry_date' => $tenants['entry_date'],
            'rent_date' => $days
            
            
        );
    }
    
    $tenants = array(
        'aaData' => $data
    );
    $app->response()->header('content-type', 'application/json');
    
    echo json_encode($tenants);
});
$app->get('/owners/search/:id', function($id = null) use ($app, $db)
{
    
    $data = array();
    
    foreach ($db->owners()->where("owner_name LIKE ?", "%" . $id . "%") as $owners) {
        
        
        $data[] = array(
            'id' => $owners['id'],
            'address' => $owners['owner_address'],
            'name' => $owners['owner_name'],
            'phone' => $owners['owner_phone'],
            'email' => $owners['owner_email']
            
            
            
        );
    }
    
    $tenants = array(
        'aaData' => $data
    );
    $app->response()->header('content-type', 'application/json');
    
    echo json_encode($tenants);
});

//Post method to insert data into database

$app->post('/houses', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->houses()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data['id']);
    
});
$app->post('/houses_tenants', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->houses_tenants()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data);
    
});
$app->post('/houses_deposits', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->houses_deposits()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data);
    
});
$app->post('/owners', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->owners()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data['id']);
    
});
$app->post('/deposits', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->deposits()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data['id']);
    
});
$app->post('/tenants', function() use ($app, $db)
{
    
    $array = (array) json_decode($app->request()->getBody());
    
    
    $data = $db->tenants()->insert($array);
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($data['id']);
    //echo json_encode($array);
    
});
$app->post('/newtenant', function() use ($app, $db)
{
     $array = (array) json_decode($app->request()->getBody());
     $person   = $db->tenants()->where('email', $array['email']);
    $count    = count($person);
    if($count == 0){
   
    
    
    $data = $db->tenants()->insert($array);
    if($data['id'] ==null){
         $response = array(
        'status' => 'false',
        'message' => "Some problem in signup. Please try again"  

        );

    }
        else{
        $response = array(
        'status' => $data['id'],
        'message' => "successfully registered.You can login after the Homigo Verification"  

        );

   }

    }else{
     $response = array(
        'status' => 'false',
        'message' => "Email already exists"  

        );
     

    }
    
    
    $app->response()->header('Content-Type', 'application/json');
    
    echo json_encode($response);
    //echo json_encode($array);
    
});


//Put method to update the data into database

$app->put('/houses/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = $app->request()->put();
        $info = array(
            "house_rent_amount" => $post['house_rent_amount']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $post,
        "message" => "data updated successfully"
    ));
});
$app->put('/updatehouses/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
       $post = (array) json_decode($app->request()->getBody());
        $info = array(
             
                'name' => $post['name'],
                'address' => $post['address'],
               
                'entry_date' => $post['entrydate'],
                'rent' => $post['rent'],
                'totalrooms' => $post['totalrooms'],
                'totaldeposit' => $post['totaldeposit'],
                'depositleft' => $post['depositleft'],
                'dthbill' => $post['dthbill'],
                'dthbilldate' => $post['dthdate'],
               
                'powerbill' => $post['powerbill'],
                'powerbilldate' => $post['powerdate'],
                
               
                'wifibill' => $post['wifibill'],
                'wifibilldate' => $post['wifidate']
                
                
                
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $post,
        "message" => "data updated successfully"
    ));
  
});
$app->put('/updatetenants/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->tenants()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
       $post = (array) json_decode($app->request()->getBody());
        $info = array(
             
                 
                'address' => $post['address'],
                'name' => $post['name'],
                'phone' => $post['phone'],
                'company' => $post['company'],
                'rent' => $post['rent'],
                'totaldeposit' => $post['totaldeposit'],
                'depositleft' => $post['depositleft'],
                'entry_date' => $post['entry_date']
                
                
                
                
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $post,
        "message" => "data updated successfully"
    ));
  
});
$app->put('/tenants/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->tenants()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "entry_date" => $post['entry_date']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($body)
    ;
});
$app->put('/houses/rent/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "entry_date" => $post['entry_date']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/houses/deposits/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->deposits()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "status" => '1'
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/houses/electricity/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "powerbilldate" => $post['entry_date']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/houses/wifi/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "wifibilldate" => $post['entry_date']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/houses/owner/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "owners_id" => $post['owners_id']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});
$app->put('/houses/dth/:id', function($id = null) use ($app, $db)
{
    
    $houses = $db->houses()->where('id', $id);
    
    $app->response()->header('Content-Type', 'application/json');
    $data = null;
    
    
    
    
    if ($houses->fetch()) {
        
        
        /*
         * We are reading JSON object received in HTTP request body and converting it to array
         */
        $post = (array) json_decode($app->request()->getBody());
        $info = array(
            "dthbilldate" => $post['entry_date']
        );
        
        /*
         * Updating Person
         */
        $data = $houses->update($info);
    }
    
    echo json_encode(array(
        "status" => (bool) $data,
        "message" => "data updated successfully"
    ));
    // echo json_encode($post);
    ;
});

//Delete method to delete the data into database
$app->delete('/person/:id', function($id) use ($app, $db)
{
    /*
     * Fetching Person for deleting
     */
    $person = $db->person()->where('id', $id);
    
    $data = null;
    if ($person->fetch()) {
        /*
         * Deleting Person
         */
        $data = $person->delete();
    }
    
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
});
$app->post("/sendmail", function () use ($app, $db) {
     
     $post = (array) json_decode($app->request()->getBody());
    
     $email_from = $post['email'];    
     $email_subjectr = "New Complaint";
     $email_tor = 'support@homigo.in';    
    
     $data ;
    
    
   
        
         
     $headers3 = 'From:' .$email_from. " ".'<'.$email_from.'>'."\r\n";     
     $headers3 .= 'Reply-To: '. $email_from. "\r\n";
    $headers3 .= "MIME-Version: 1.0\r\n";
     $headers3 .= "Content-Type: text/html; charset=ISO-8859-1\r\n";   
        $body  = '

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Really Simple HTML Email Template</title>
<style type="text/css">
/* ------------------------------------- 
		GLOBAL 
------------------------------------- */
* { 
	margin:0;
	padding:0;
	font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; 
	font-size: 100%;
	line-height: 1.6;
}

img { 
	max-width: 100%; 
}

body {
	-webkit-font-smoothing:antialiased; 
	-webkit-text-size-adjust:none; 
	width: 100%!important; 
	height: 100%;
}


/* ------------------------------------- 
		ELEMENTS 
------------------------------------- */
a { 
	color: #348eda;
}

.btn-primary, .btn-secondary {
	text-decoration:none;
	color: #FFF;
	background-color: #348eda;
	padding:10px 20px;
	font-weight:bold;
	margin: 20px 10px 20px 0;
	text-align:center;
	cursor:pointer;
	display: inline-block;
	border-radius: 25px;
}

.btn-secondary{
	background: #aaa;
}

.last { 
	margin-bottom: 0;
}

.first{
	margin-top: 0;
}


/* ------------------------------------- 
		BODY 
------------------------------------- */
table.body-wrap { 
	width: 100%;
	padding: 20px;
}

table.body-wrap .container{
	border: 1px solid #f0f0f0;
}


/* ------------------------------------- 
		FOOTER 
------------------------------------- */
table.footer-wrap { 
	width: 100%;	
	clear:both!important;
}

.footer-wrap .container p {
	font-size:12px;
	color:#666;
	
}

table.footer-wrap a{
	color: #999;
}


/* ------------------------------------- 
		TYPOGRAPHY 
------------------------------------- */
h1,h2,h3{
	font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; line-height: 1.1; margin-bottom:15px; color:#000;
	margin: 40px 0 10px;
	line-height: 1.2;
	font-weight:200; 
}

h1 {
	font-size: 36px;
}
h2 {
	font-size: 28px;
}
h3 {
	font-size: 22px;
}

p, ul { 
	margin-bottom: 10px; 
	font-weight: normal; 
	font-size:14px;
}

ul li {
	margin-left:5px;
	list-style-position: inside;
}

/* --------------------------------------------------- 
		RESPONSIVENESS
		Nuke it from orbit. Its the only way to be sure. 
------------------------------------------------------ */

/* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
.container {
	display:block!important;
	max-width:600px!important;
	margin:0 auto!important; /* makes it centered */
	clear:both!important;
}

/* This should also be a block element, so that it will fill 100% of the .container */
.content {
	padding:20px;
	max-width:600px;
	margin:0 auto;
	display:block; 
}

/* Lets make sure tables in the content area are 100% wide */
.content table { 
	width: 100%; 
}

</style>
</head>
 
<body bgcolor="#f6f6f6">

<!-- body -->
<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container" bgcolor="#FFFFFF">

			<!-- content -->
			<div class="content">
			<table>
				<tr>
					<td>
						<p>Hi Admin,</p>
						<p>Complaint Details are given below</p>
						<p>Tenant name : <a href="http://ankitsilaich.in/admin-homigo/#/app/tenant/details/'.$post['id'].'" class="btn-primary"> '.$post['name'].'</a></p>
						
						<p>complaint : '.$post['complaint'].'</p>
						
						<p>All the information you need is on below link.</p>
						<p><a href="http://ankitsilaich.in/admin-homigo/#/app/tenant/details/'.$post['id'].'" class="btn-primary">View the details and on  admin Pannel</a></p>
						
						<p>Thanks, have a lovely day.</p>
						
					</td>
				</tr>
			</table>
			</div>
			<!-- /content -->
									
		</td>
		<td></td>
	</tr>
</table>
<!-- /body -->

<!-- footer -->
<table class="footer-wrap">
	<tr>
		<td></td>
		<td class="container">
			
			<!-- content -->
			<div class="content">
				<table>
					<tr>
						<td align="center">
							<p>Developed by <a href="https://facebook.com/ankitkumarsilaich"><unsubscribe>Ankit Silaich</unsubscribe></a>.
							</p>
						</td>
					</tr>
				</table>
			</div><!-- /content -->
				
		</td>
		<td></td>
	</tr>
</table>
<!-- /footer -->

</body>
</html>';
    
    $headersr = 'From: '.$email_from."\r\n".
     'Reply-To: '.$email_from."\r\n" .
     'X-Mailer: PHP/' . phpversion();
     mail($email_tor, $email_subjectr, $body , $headers3);
     
        $data = array("status"=> "email sent success");
    
        $app->response()->header('Content-Type', 'application/json');
    echo json_encode($data);
});

$app->run();