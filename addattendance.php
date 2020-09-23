<?php
    require_once('../includes/db.php');
    require_once('../includes/functions.php');
    
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    
    if(
        $_SERVER['REQUEST_METHOD'] !== 'POST' ||
        !has_no_array($_POST['fpid'], $_POST['hash'])
    ) {
        header('HTTP/1.1 404 Not Found');
    } else {
        $tz = new DateTimeZone('Asia/Kuala_Lumpur');
        if(
            $_POST['hash'] !== hash_hmac('sha1', $_POST['fpid'].(new DateTime("now",$tz))->format('YnjGi'), 'rsc_fpbiometrics_pw216') &&
            $_POST['hash'] !== hash_hmac('sha1', $_POST['fpid'].(new DateTime("-1 min",$tz))->format('YnjGi'), 'rsc_fpbiometrics_pw216')
        ) {
            header('HTTP/1.1 403 Forbidden');
        } else {
            $stmt = $conn->prepare('SELECT staffid,staff_name FROM '.$db_prefix.'staff WHERE fingerprintid=?');
            $stmt->bind_param('s',$_POST['fpid']);
            if(($stmt->execute()) === false) {
                header('HTTP/1.1 500 Internal Server Error');
            } else {
                $res = $stmt->get_result();
                if($res->num_rows === 0) {
                    echo '<center><h1>Error</h1><p>Fingerprint is enrolled but not registered into the staff management system database. Please inform an administrator to enroll your fingerprint ID into the system.<br><br>Fingerprint ID: ' . $_POST['fpid'] . '</p><br><br><button style="width:80%;background-color:blue;height:100px" onclick="window.location.reload()"><font size="50">Done</font></button></center>';
                } elseif($res->num_rows > 1) {
                    echo '<center><h1>Error</h1><p>This fingerprint is associated with more than one staff in the staff management system database. Please inform an administrator to fix the data conflict in the system.<br><br>Fingerprint ID: ' . $_POST['fpid'] . '</p><br><br><button style="width:80%;background-color:blue;height:100px" onclick="window.location.reload()"><font size="50">Done</font></button></center>';
                } else {
                    $row = $res->fetch_array();
                    $staffid = $row[0];
                    $staffname = $row[1];
                    $stmt = $conn->prepare('SELECT time,type FROM '.$db_prefix.'attendance WHERE staffid=? ORDER BY time DESC LIMIT 1');
                    $stmt->bind_param('i',$staffid);
                    if(($stmt->execute()) !== false) {
                        $res = $stmt->get_result();
                        $row = $res->fetch_assoc();
                        if((strtotime('now') - strtotime('-8 hours', strtotime($row['time'])))/60 <= 30) {
                            echo '<center><h1>Error</h1><table><tr><th style="text-align:left">Fingerprint ID: </th><td>' . $_POST['fpid'] . '</td><tr><th style="text-align:left">Staff ID: </th><td>' . $staffid . '</td></tr><th style="text-align:left">Staff Name: </th><td>' . $staffname . '</td></table><p>You have already clock in recently. Please be aware that there is a 30 minutes cooldown period before being able to use the time attendance system again.</p></center><script>setTimeout(function(){ window.location.reload(); }, 5000);</script>';
                        } else {
                            if($row['type'] == 0) {
                                $stmt = $conn->prepare('INSERT INTO '.$db_prefix.'attendance(staffid,time,type) VALUES (?,?,1)');
                                $tmp = (new DateTime('now',$tz))->format('Y-m-d H:i:s');
                                $stmt->bind_param('is',$staffid,$tmp);
                                if(($stmt->execute()) !== false) {
                                    echo '<center><h1>Welcome!</h1><table><tr><th style="text-align:left">Fingerprint ID: </th><td>' . $_POST['fpid'] . '</td><tr><th style="text-align:left">Staff ID: </th><td>' . $staffid . '</td></tr><th style="text-align:left">Staff Name: </th><td>' . $staffname . '</td></table><p>Your attendance is taken successfully.</p></center><script>setTimeout(function(){ window.location.reload(); }, 3000);</script>';
                                } else {
                                    header('HTTP/1.1 500 Internal Server Error');
                                }
                            } else {
                                $stmt = $conn->prepare('INSERT INTO '.$db_prefix.'attendance(staffid,time,type) VALUES (?,?,0)');
                                $tmp = (new DateTime('now',$tz))->format('Y-m-d H:i:s');
                                $stmt->bind_param('is',$staffid,$tmp);
                                if(($stmt->execute()) !== false) {
                                    echo '<center><h1>Goodbye!</h1><table><tr><th style="text-align:left">Fingerprint ID: </th><td>' . $_POST['fpid'] . '</td><tr><th style="text-align:left">Staff ID: </th><td>' . $staffid . '</td></tr><th style="text-align:left">Staff Name: </th><td>' . $staffname . '</td></table><p>Your attendance is taken successfully.</p></center><script>setTimeout(function(){ window.location.reload(); }, 3000);</script>';
                                } else {
                                    header('HTTP/1.1 500 Internal Server Error');
                                }
                            }
                        }
                    } else {
                        header('HTTP/1.1 500 Internal Server Error');
                    }
                }
            }
        }
    }
?>