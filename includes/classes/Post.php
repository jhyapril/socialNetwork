<?php 
class Post {
    private $user_obj;
    private $con;

    public function __construct($con, $user) {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function submitPost($body, $user_to) {
        $body = strip_tags($body); // Removes html tags
        $body = mysqli_real_escape_string($this->con, $body);
        // replace by match string
        $body = str_replace('\r\n', '\n', $body);
        // replace newline with line break \n -> <br/>
        $body = nl2br($body);
        // replace using regex
        $check_empty = preg_replace('/\s+/', '', $body); // Deletes all spaces
        if($check_empty != "") {
            // Current date and time
            $date_added = date("Y-m-d H:i:s");
            // Get usernmae
            $added_by = $this->user_obj->getUsername();

            // if user is on own profile, user_to is 'none'
            if($user_to === $added_by) {
                $user_to = "none";
            }

            // insert post
            $query = mysqli_query($this->con, "INSERT INTO posts VALUES(NULL, '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0')");
            $returned_id = mysqli_insert_id($this->con);

            // Insert notification

            // update post count for user
            $num_posts = $this->user_obj->getNumPosts();
            $num_posts++;
            $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by");
        }
    }

    public function loadPostsFriends($data, $limit) {
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        if ($page == 1) {
            $start = 0;
        } else {
            $start = ($page - 1) * $limit;
        }
        $str = "";
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");
        if (mysqli_num_rows($data_query) > 0) {
            $num_iterations = 0; // no of results checked (not necessarily posted)
            $count = 1;
            while($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];
                // Prepare user_to string so it can be included even if not posted to a user
                if($row['user_to'] = "none") {
                    $user_to = "";
                } else {
                    $user_to_obj = new User($this->con, $row['user_to']);
                    $user_to_name = $user_to_obj->getFullName();
                    $user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>"; 
                }
                // Check if user who posted has their account closed
                $added_by_obj = new User($this->con, $added_by);
                if ($added_by_obj->isClosed()) {
                    continue;
                }
                $user_logged_obj = new User($this->con, $userLoggedIn);
                if ($user_logged_obj->isFriend($added_by)) {
                    if ($num_iterations ++ < $start) {
                        continue;
                    }
                    // Once $limit posts have been loaded, break
                    if ($count > $limit) {
                        break;
                    } else {
                        $count ++;
                    }
                    $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
                    $user_row = mysqli_fetch_array($user_details_query);
                    $first_name = $user_row['first_name'];
                    $last_name = $user_row['last_name'];
                    $profile_pic = $user_row['profile_pic'];
                    // Timeframe
                    $post_date = new DateTime($date_time);
                    $time_message = $this->getTimeMessage($post_date);
                    $str .= "<div class='status_post'>
                                <div class='post_profile_pic'>
                                    <img src='$profile_pic' width='50'>
                                </div>
                                <div class='posted_by' style='color:#ACACAC;'>
                                    <a href='$added_by'>$first_name $last_name</a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                </div>
                                <div id='post_body'>
                                    $body
                                    <br />
                                </div>
                            </div>
                            <hr />";
                }
            }
            if ($count > $limit) {
                $str .= "<input type='hidden' class='nextPage' value='" . ($page+1) . "'>
                    <input type='hidden' class='noMorePosts' value='false'>";
            } else {
                $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show. </p>";
            }
        }
        echo $str;
    }

    public function getTimeMessage($post_date) {
        $date_time_format = date("Y-m-d H:i:s");
        $current_date = new DateTime($date_time_format);
        $diff = $post_date->diff($current_date);
        $year = $diff->y;
        if ($year >= 1) {
            $year_word = $year == 1 ? " year ago" : " years ago";
            return $year . $year_word;
        } 
        $month = $diff->m;
        if ($month >= 1) {
            $month_word = $month == 1 ? " month ago" : " months ago";
            return $month . $month_word;
        }
        $day = $diff->d;
        if ($day >= 1) {
            if ($day == 1) {
                return "Yesterday";
            }
            return $day . " days ago";
        }
        $hour = $diff->h;
        if ($hour >= 1) {
            $hour_word = $hour == 1 ? " hour ago" : " hours ago";
            return $hour . $hour_word;
        }
        $min = $diff->i;
        if ($min >= 1) {
            $min_word = $min == 1 ? " minute ago" : " minutes ago";
            return $min . $min_word;
        }
        $sec = $diff->s;
        if ($sec < 30) {
            return "Just now";
        }
        return $sec . " seconds ago";
    }
}
?>