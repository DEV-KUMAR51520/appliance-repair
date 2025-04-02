<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkRole(['admin', 'customer', 'technician']);

$pageTitle = "Select Location";
require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">Select Your Location</h1>

    <div class="mb-4">
        <button id="detectLocation" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Auto Detect Location
        </button>
    </div>

    <div id="map" class="w-full h-96 border rounded-lg shadow-md"></div>

    <form method="POST" action="save-location.php" class="mt-4">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        
        <div class="mt-4">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Save Location
            </button>
        </div>
    </form>
</div>

<script>
let map, marker;

function initMap() {
    const defaultLocation = { lat: 28.6139, lng: 77.2090 }; // Default to New Delhi
    
    map = new google.maps.Map(document.getElementById("map"), {
        center: defaultLocation,
        zoom: 12,
    });

    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: true
    });

    // Update hidden fields when marker is dragged
    google.maps.event.addListener(marker, 'dragend', function() {
        document.getElementById('latitude').value = marker.getPosition().lat();
        document.getElementById('longitude').value = marker.getPosition().lng();
    });

    // Auto-detect location
    document.getElementById("detectLocation").addEventListener("click", function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    map.setCenter(userLocation);
                    marker.setPosition(userLocation);

                    document.getElementById('latitude').value = userLocation.lat;
                    document.getElementById('longitude').value = userLocation.lng;
                },
                function() {
                    alert("Geolocation failed. Please select location manually.");
                }
            );
        } else {
            alert("Your browser does not support Geolocation.");
        }
    });
}
</script>

<!-- Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap"></script>

<?php require_once '../includes/footer.php'; ?>
