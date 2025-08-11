function checkIP(file, redirectUrl) {
    $.ajax({
        url: 'check_ip.php',
        method: 'GET',
        data: { file: file },
        dataType: 'json',
        cache: false,  // Prevent caching
        success: function(response) {
            if (response.banned) {
                window.location.href = redirectUrl;
            }
        },
        error: function(xhr, status, error) {
            console.error('Error checking IP:', error);
        }
    });
}
