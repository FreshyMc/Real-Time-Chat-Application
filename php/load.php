<?php
$file_path = './php/messages.txt';

$messages = explode("\r\n", file_get_contents($file_path));

ob_start();
?>
<?php foreach($messages as $message): ?>
    <?php 
        $decoded = json_decode($message, true); 
        
        if(!empty($decoded)):
            if($decoded['token']!=$user_token):
    ?>
    <div class="message-row receiver">
    <?php 
            else:
    ?>
    <div class="message-row sender">
    <?php 
            endif;
    ?>
        <div class="message">
            <div class="avatar rounded d-inline-flex align-items-center">
                <img src="./assets/img/user.svg" width="40" height="40" alt="...">
                <span class="px-2"><?php echo $decoded['username']; ?></span>
            </div>
            <?php if($decoded['type']==1): ?>
            <p><?php echo $decoded['message']; ?></p>
            <?php else: ?>
            <p class="file-message"><a href="javascript:void(0);" data-token="<?php echo $decoded['file_token'] ?>" class="view-file">View file message</a></p>
            <?php endif; ?>
            <div class="message-timestamp">
                <?php echo $decoded['time']; ?>
            </div>
        </div>
    </div>
    <?php
        endif; 
    ?>
<?php endforeach; ?>
<?php flush(); ?>