<?php
/*
Plugin Name: Curiosity POTD
Plugin Script: curiosity-potd.php
Description: NASA Curiosity rover picture of the day widget.
Version: 1.0
License: GPL 2.0
Author: Andrea Benzi
Author URI: http://www.andreabenzi.it
*/

class curiosity_potd_widget extends WP_Widget
{
    public function __construct(){
        $widget_options = array( 
            'classname' => 'curiosity_potd',
            'description' => 'NASA Curiosity rover picture of the day widget.',
        );
        parent::__construct( 'curiosity_potd', 'Curiosity POTD', $widget_options );
    }

    public function form( $instance ){
        $defaults = array(
            'title' => 'Curiosity Today',
            'api_key' => 'DEMO_KEY'
        );
 
        $instance = wp_parse_args( (array) $instance, $defaults ); 
        ?>
 
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                Titolo:
            </label>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'api_key' ); ?>">
                API Key:
            </label>
            <input type="text" id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" value="<?php echo $instance['api_key']; ?>" />
        </p>
 
        <?php
    }

 
    public function widget( $args, $instance ){
 
        extract($args);
 
        $title = apply_filters('widget_title', $instance['title'] );
 
        echo $before_widget;
        echo $before_title . $title . $after_title;

        ?>
            <script>
                window.onload = function (){
                    
                    //Set yesterday date
                    let today = new Date();
                    let yesterday = new Date(today);
                    yesterday.setDate(today.getDate() - 2);
                    let dd = yesterday.getDate();
                    let mm = yesterday.getMonth()+1;
                    let yyyy = yesterday.getFullYear();
                    if(dd<10){
                        dd='0'+dd
                    } 
                    if(mm<10){
                        mm='0'+mm
                    } 

                    //Call NASA Curiosity API
                    let xhr_curiosity = new XMLHttpRequest();
                    xhr_curiosity.open('GET','https://api.nasa.gov/mars-photos/api/v1/rovers/curiosity/photos?earth_date='+yyyy+'-'+mm+'-'+dd+'&api_key=<?php echo $instance['api_key']; ?>');
                    xhr_curiosity.send();
                    
                    xhr_curiosity.onreadystatechange = function(){
                        if(xhr_curiosity.readyState == 4 && xhr_curiosity.status === 200){
                            
                            let obj_curiosity = JSON.parse(xhr_curiosity.responseText);
                            
                            if (obj_curiosity.photos.length != 0){
                                
                                //Create img element
                                var img_curiosity = document.createElement("img");
                                img_curiosity.src = obj_curiosity.photos[0].img_src;
                                img_curiosity.style.width = '100%';
                                document.querySelector('#curiosity-potd').appendChild(img_curiosity);

                                //Create sol text
                                var sol = document.createElement("I");
                                var sol_text = document.createTextNode("Sol: "+obj_curiosity.photos[0].sol+" ("+dd+"/"+mm+"/"+yyyy+")");
                                sol.appendChild(sol_text);                               
                                document.querySelector('#curiosity-potd').appendChild(sol);

                                var br = document.createElement("BR");
                                document.querySelector('#curiosity-potd').appendChild(br);
                                
                                //Create camera name text
                                var sol = document.createElement("SMALL");
                                var sol_text = document.createTextNode(obj_curiosity.photos[0].camera.full_name);
                                sol.appendChild(sol_text);                               
                                document.querySelector('#curiosity-potd').appendChild(sol);

                            }else{
                                var error = document.createElement("SMALL");
                                var error_text = document.createTextNode('Sorry. No picture today.');
                                error.appendChild(error_text);           
                                document.querySelector('#curiosity-potd').appendChild(error);
                            }

                        }
                    }
                    
                    xhr_curiosity.error = () => {
                        var error = document.createElement("SMALL");
                        var error_text = document.createTextNode('Sorry. Error contacting server.');
                        error.appendChild(error_text);           
                        document.querySelector('#curiosity-potd').appendChild(error);
                    }
                }
            </script>    
            <div id="curiosity-potd"></div>
        
        <?php
        echo $after_widget;
    }

    public function update( $new_instance, $old_instance ){
        $instance = $old_instance;
 
        $instance['title'] = strip_tags( $new_instance['title'] );

        $instance['api_key'] = strip_tags( $new_instance['api_key'] );
 
        return $instance;
    }

 
}

function curiosity_potd_register_widgets()
{
    register_widget( 'curiosity_potd_widget' );
}

add_action( 'widgets_init', 'curiosity_potd_register_widgets' );
?>