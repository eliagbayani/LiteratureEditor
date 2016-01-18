<?php
// print_r($options);
?>
<table border="0">
<tr>
    <td>
        <?php echo $options['text'] ?>
    </td>
    <td>
        <?php
        if($val = @$options['href'])
        {
            ?>
            <a href="<?php echo $val ?>">
            <img src="<?php echo $options['src'] ?>" title="<?php echo $options['alt_text'] ?>">
            </a>
            <?php
        }
        else
        {
            ?>
            <img src="<?php echo $options['src'] ?>" title="<?php echo $options['alt_text'] ?>">
            <?php
        }
        ?>
    </td>
</tr>
</table>