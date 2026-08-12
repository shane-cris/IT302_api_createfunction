<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/includes/helpers.php';

require_user();

/**
 * Handle the reservation form submission.
 */
if (isset($_POST['guestdetailsubmit'])) {
    $fields = ['Name', 'Email', 'Country', 'Phone', 'RoomType', 'Bed', 'NoofRoom', 'Meal', 'cin', 'cout'];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = trim($_POST[$field] ?? '');
    }

    if ($data['Name'] === '' || $data['Email'] === '' || $data['Country'] === '' || $data['cin'] === '' || $data['cout'] === '') {
        echo "<script>swal({ title: 'Please fill in the proper details', icon: 'error' });</script>";
    } else {
        $sql = "INSERT INTO roombook
                    (Name, Email, Country, Phone, RoomType, Bed, NoofRoom, Meal, cin, cout, stat, nodays)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'NotConfirm', DATEDIFF(?, ?))";

        $id = insert_row($sql, 'ssssssssssss', [
            $data['Name'], $data['Email'], $data['Country'], $data['Phone'],
            $data['RoomType'], $data['Bed'], $data['NoofRoom'], $data['Meal'],
            $data['cin'], $data['cout'], $data['cout'], $data['cin'],
        ]);

        if ($id > 0) {
            echo "<script>swal({ title: 'Reservation successful! We will reach out shortly.', icon: 'success' });</script>";
        } else {
            echo "<script>swal({ title: 'Something went wrong, please try again', icon: 'error' });</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Blue Bird</title>
    <link rel="stylesheet" href="./css/home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body>
    <nav class="navbar-glass">
        <div class="nav-logo">
            <img src="./image/bluebirdlogo.png" alt="Blue Bird logo">
            <span>BLUEBIRD</span>
        </div>
        <div class="nav-links">
            <ul>
                <li><a href="#firstsection">Home</a></li>
                <li><a href="#secondsection">Rooms</a></li>
                <li><a href="#thirdsection">Facilities</a></li>
                <li><a href="#contactus">Contact</a></li>
            </ul>
            <a href="./logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <!-- Hero -->
    <section id="firstsection" class="carousel slide carousel_section" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="./image/hotel1.jpg" alt="Hotel exterior">
            </div>
            <div class="carousel-item">
                <img src="./image/hotel2.jpg" alt="Hotel lobby">
            </div>
            <div class="carousel-item">
                <img src="./image/hotel3.jpg" alt="Hotel suite">
            </div>
            <div class="carousel-item">
                <img src="./image/hotel4.jpg" alt="Pool view">
            </div>
        </div>

        <div class="hero-content">
            <span class="hero-eyebrow">EST. ★★★★★</span>
            <h1 class="hero-title">Welcome to heaven on earth</h1>
            <p class="hero-sub">Luxury stays, world-class comfort and memories that last forever.</p>
            <a href="#secondsection" class="btn hero-cta">Explore Rooms</a>
        </div>
    </section>

    <!-- Rooms -->
    <section id="secondsection">
        <div class="section-bg"><img src="./image/homeanimatebg.svg" alt=""></div>
        <div class="container py-5">
            <h2 class="head">Our Rooms</h2>

            <div class="roomselect">
                <div class="roombox">
                    <div class="hotelphoto h1"></div>
                    <div class="roomdata">
                        <h3>Superior Room</h3>
                        <div class="services">
                            <span><i class="fa-solid fa-wifi"></i> Wi-Fi</span>
                            <span><i class="fa-solid fa-burger"></i> Dining</span>
                            <span><i class="fa-solid fa-spa"></i> Spa</span>
                        </div>
                        <button class="btn btn-gold bookbtn" onclick="openbookbox()">Book now</button>
                    </div>
                </div>

                <div class="roombox">
                    <div class="hotelphoto h2"></div>
                    <div class="roomdata">
                        <h3>Deluxe Room</h3>
                        <div class="services">
                            <span><i class="fa-solid fa-wifi"></i> Wi-Fi</span>
                            <span><i class="fa-solid fa-burger"></i> Dining</span>
                            <span><i class="fa-solid fa-spa"></i> Spa</span>
                        </div>
                        <button class="btn btn-gold bookbtn" onclick="openbookbox()">Book now</button>
                    </div>
                </div>

                <div class="roombox">
                    <div class="hotelphoto h3"></div>
                    <div class="roomdata">
                        <h3>Guest House</h3>
                        <div class="services">
                            <span><i class="fa-solid fa-wifi"></i> Wi-Fi</span>
                            <span><i class="fa-solid fa-burger"></i> Dining</span>
                        </div>
                        <button class="btn btn-gold bookbtn" onclick="openbookbox()">Book now</button>
                    </div>
                </div>

                <div class="roombox">
                    <div class="hotelphoto h4"></div>
                    <div class="roomdata">
                        <h3>Single Room</h3>
                        <div class="services">
                            <span><i class="fa-solid fa-wifi"></i> Wi-Fi</span>
                        </div>
                        <button class="btn btn-gold bookbtn" onclick="openbookbox()">Book now</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities -->
    <section id="thirdsection">
        <div class="container">
            <h2 class="head">Facilities</h2>
            <div class="facility">
                <div class="box"><h3>Swimming Pool</h3></div>
                <div class="box"><h3>Spa & Wellness</h3></div>
                <div class="box"><h3>24×7 Restaurants</h3></div>
                <div class="box"><h3>24×7 Gym</h3></div>
                <div class="box"><h3>Heli Service</h3></div>
            </div>
        </div>
    </section>

    <!-- Contact / footer -->
    <footer id="contactus">
        <div class="social">
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
            <a href="mailto:hello@bluebird.com" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
        </div>
        <div class="createdby">© <?php echo date('Y'); ?> Hotel Blue Bird</div>
    </footer>

    <!-- Reservation modal -->
    <div id="guestdetailpanel">
        <div class="guestdetailpanelform">
            <form action="" method="POST" class="reservation-form">
                <div class="head">
                    <h3>Reservation</h3>
                    <i class="fa-solid fa-circle-xmark" onclick="closebox()"></i>
                </div>
                <div class="middle">
                    <div class="guestinfo">
                        <h4>Guest information</h4>
                        <div class="field">
                            <label>Full name</label>
                            <input type="text" name="Name" placeholder="Enter full name" required>
                        </div>
                        <div class="field">
                            <label>Email</label>
                            <input type="email" name="Email" placeholder="you@example.com" required>
                        </div>
                        <div class="field">
                            <label>Country</label>
                            <select name="Country" class="selectinput" required>
                                <option value="" selected disabled>Select your country</option>
                                <?php foreach (countries() as $country): ?>
                                    <option value="<?php echo e($country); ?>"><?php echo e($country); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label>Phone</label>
                            <input type="text" name="Phone" placeholder="Enter phone number">
                        </div>
                    </div>

                    <div class="line"></div>

                    <div class="reservationinfo">
                        <h4>Reservation information</h4>
                        <div class="field">
                            <label>Room type</label>
                            <select name="RoomType" class="selectinput" required>
                                <option value="" selected disabled>Type of room</option>
                                <option value="Superior Room">Superior Room</option>
                                <option value="Deluxe Room">Deluxe Room</option>
                                <option value="Guest House">Guest House</option>
                                <option value="Single Room">Single Room</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Bedding type</label>
                            <select name="Bed" class="selectinput" required>
                                <option value="" selected disabled>Bedding type</option>
                                <option value="Single">Single</option>
                                <option value="Double">Double</option>
                                <option value="Triple">Triple</option>
                                <option value="Quad">Quad</option>
                                <option value="None">None</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Meal plan</label>
                            <select name="Meal" class="selectinput" required>
                                <option value="" selected disabled>Meal</option>
                                <option value="Room only">Room only</option>
                                <option value="Breakfast">Breakfast</option>
                                <option value="Half Board">Half Board</option>
                                <option value="Full Board">Full Board</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Number of rooms</label>
                            <select name="NoofRoom" class="selectinput">
                                <option value="1" selected>1</option>
                            </select>
                        </div>
                        <div class="datesection">
                            <div class="field">
                                <label>Check-in</label>
                                <input type="date" name="cin" required>
                            </div>
                            <div class="field">
                                <label>Check-out</label>
                                <input type="date" name="cout" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <button type="submit" name="guestdetailsubmit" class="btn btn-submit">Submit reservation</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        var bookbox = document.getElementById("guestdetailpanel");

        function openbookbox() {
            bookbox.style.display = "flex";
        }

        function closebox() {
            bookbox.style.display = "none";
        }

        bookbox.addEventListener("click", function (e) {
            if (e.target === bookbox) closebox();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>