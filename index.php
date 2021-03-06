<?php
ob_start();
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.5/css/select.dataTables.min.css">
    <link rel="stylesheet" href="css/main.css">

    <?php

    require 'lib/phpPasswordHashing/passwordLib.php';
    require 'app/DB.php';
    require 'app/Util.php';
    require 'app/dao/CustomerDAO.php';
    require 'app/dao/BookingDetailDAO.php';
    require 'app/models/Customer.php';
    require 'app/models/Booking.php';
    require 'app/models/Reservation.php';
    require 'app/handlers/CustomerHandler.php';
    require 'app/handlers/BookingDetailHandler.php';

    $username = null;
    $isSessionExists = false;
    $cHandler = null;
    $bdHandler = null;
    $cBookings = null;
    if (isset($_SESSION["username"]))
    {
        $username = $_SESSION["username"];
        $isSessionExists = true;

        $cHandler = new CustomerHandler();
        $cHandler = $cHandler->getCustomerObj($_SESSION["customerEmail"]);

        $cAdmin = new Customer();
        $cAdmin->setEmail($cHandler->getEmail());
        $isAdmin = $cAdmin->isAdminSignedIn();

        $bdHandler = new BookingDetailHandler();
        $cBookings = $bdHandler->getCustomerBookings($cHandler);
    }

    ?>

    <title>Home</title>
</head>
<body>

<header>
    <div class="bg-dark collapse" id="navbarHeader" style="">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-md-7 py-4">
                    <h4 class="text-white">About</h4>
                    <p class="text-muted">Add some information about hotel booking.</p>
                </div>
                <div class="col-sm-4 offset-md-1 py-4 text-right">
                    <?php if ($isSessionExists) { ?>
                    <h4 class="text-white"><?php echo $username; ?></h4>
                    <ul class="list-unstyled">
                        <?php if ($isAdmin) { ?>
                        <li><a href="admin.php" class="text-white">Manage reservation<i class="far fa-address-book ml-2"></i></a></li>
                        <?php } else { ?>
                        <li><a href="#" class="text-white my-reservations">View my bookings<i class="far fa-address-book ml-2"></i></a></li>
                        <li>
                            <a href="#" class="text-white" data-toggle="modal" data-target="#myProfileModal">Update profile<i class="fas fa-user ml-2"></i></a>
                        </li>
                        <?php } ?>
                        <li><a href="#" id="sign-out-link" class="text-white">Sign out<i class="fas fa-sign-out-alt ml-2"></i></a></li>
                    </ul>
                    <?php } else { ?>
                    <h4>
                        <a class="text-white" href="sign-in.php">Sign in</a> <span class="text-white">or</span>
                        <a href="register.php" class="text-white">Register </a>
                    </h4>
                    <p class="text-muted">Log in so you can take advantage with our hotel room prices.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="navbar navbar-dark bg-dark box-shadow">
        <div class="container d-flex justify-content-between">
            <a href="#" class="navbar-brand d-flex align-items-center">
                <i class="fas fa-h-square mr-2"></i>
                <strong>Hotel Booking</strong>
            </a>
            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
    <div class="container my-3" id="my-reservations-div">
        <h4>Reservations</h4>
        <table id="myReservationsTbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th class="text-hide p-0" data-bookId="12">12</th>
                <th scope="col">Start date</th>
                <th scope="col">End date</th>
                <th scope="col">Room type</th>
                <th scope="col">Requirements</th>
                <th scope="col">Adults</th>
                <th scope="col">Children</th>
                <th scope="col">Requests</th>
                <th scope="col">Timestamp</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($cBookings) && $bdHandler->getExecutionFeedback() == 1) { ?>
                <?php   foreach ($cBookings as $k => $v) { ?>
                    <tr>
                        <th scope="row"><?php echo ($k + 1); ?></th>
                        <td class="text-hide p-0"><?php echo $v["id"]; ?></td>
                        <td><?php echo $v["start"]; ?></td>
                        <td><?php echo $v["end"]; ?></td>
                        <td><?php echo $v["type"]; ?></td>
                        <td><?php echo $v["requirement"]; ?></td>
                        <td><?php echo $v["adults"]; ?></td>
                        <td><?php echo $v["children"]; ?></td>
                        <td><?php echo $v["requests"]; ?></td>
                        <td><?php echo $v["timestamp"]; ?></td>
                        <td><?php echo $v["status"]; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
</header>

<main role="main">

    <section class="jumbotron text-center">
        <div class="container pt-lg-5 pl-5 px-5">
            <h1 class="display-3">A brand new hotel beyond ordinary</h1>
            <p class="lead text-muted">Book your summer holidays with us now.</p>
            <p>
                <?php if ($isSessionExists) { ?>
                <a href="#" class="btn btn-success my-2" data-toggle="modal" data-target=".book-now-modal-lg">Book now<i class="fas fa-angle-double-right ml-1"></i></a>
                <?php } else { ?>
                <a href="#" class="btn btn-success my-2" data-toggle="modal" data-target=".sign-in-to-book-modal">Book now<i class="fas fa-angle-double-right ml-1"></i></a>
                <?php } ?>
            </p>
        </div>
    </section>

    <div class="container">
        <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
            <h1 class="display-4">Pricing</h1>
            <p class="lead">Quickly build an effective pricing table for your potential customers with this Bootstrap example. It's built with default Bootstrap components and utilities with little customization.</p>
        </div>
    </div>

    <div class="album py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        <div class="card-header">
                            <h5 class="my-0 font-weight-normal">Deluxe Room</h5>
                        </div>
                        <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" style="height: 225px; width: 100%; display: block;" src="image/deluxe.jpg" data-holder-rendered="true">
                        <div class="card-body">
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <?php if ($isSessionExists) { ?>
                                    <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".book-now-modal-lg">
                                        Book
                                    </button>
                                    <?php } else { ?>
                                    <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".sign-in-to-book-modal">
                                        Book
                                    </button>
                                    <?php } ?>
                                </div>
                                <small class="text-muted">250 / night</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        <div class="card-header">
                            <h5 class="my-0 font-weight-normal">Double Room</h5>
                        </div>
                        <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" src="image/double.jpg" data-holder-rendered="true" style="height: 225px; width: 100%; display: block;">
                        <div class="card-body">
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if ($isSessionExists) { ?>
                                <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".book-now-modal-lg">
                                    Book
                                </button>
                                <?php } else { ?>
                                <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".sign-in-to-book-modal">
                                    Book
                                </button>
                                <?php } ?>
                                <small class="text-muted">180 / night</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        <div class="card-header">
                            <h5 class="my-0 font-weight-normal">Single Room</h5>
                        </div>
                        <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" src="image/single.jpg" data-holder-rendered="true" style="height: 225px; width: 100%; display: block;">
                        <div class="card-body">
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if ($isSessionExists) { ?>
                                <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".book-now-modal-lg">
                                    Book
                                </button>
                                <?php } else { ?>
                                <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".sign-in-to-book-modal">
                                    Book
                                </button>
                                <?php } ?>
                                <small class="text-muted">130 / night</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade book-now-modal-lg" tabindex="-1" role="dialog" aria-labelledby="bookNowModalLarge" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reservation form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="reservationModalBody">
                    <?php if ($isSessionExists && !$isAdmin) { ?>
                    <form role="form" autocomplete="off" id="reservation-form" method="post">
                        <?php if ($isSessionExists) { ?>
                        <input type="number" id="cid" name="cid" value="<?php echo $cHandler->getId() ?>" hidden>
                        <?php } ?>
                        <div class="form-group row">
                            <label for="startDate" class="col-sm-3 col-form-label">From
                                <span class="red-asterisk"> *</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                    <input type="date" class="form-control" id="startDate"
                                           name="startDate" min="<?php echo Util::dateToday(); ?>" required>
                                 </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="endDate" class="col-sm-3 col-form-label">To
                                <span class="red-asterisk"> *</span>
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                    <input type="date" class="form-control" id="endDate" name="endDate" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label" for="roomType">Room type
                                <span class="red-asterisk"> *</span>
                            </label>
                            <div class="col-sm-9">
                                <select required class="custom-select mr-sm-2" id="roomType" name="roomType">
                                    <option value="deluxe">Deluxe room</option>
                                    <option value="double">Double room</option>
                                    <option value="single">Single room</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label" for="roomRequirement">Room requirements</label>
                            <div class="col-sm-9">
                                <select class="custom-select mr-sm-2" id="roomRequirement" name="roomRequirement">
                                    <option value="no preference" selected>No preference</option>
                                    <option value="non smoking">Non smoking</option>
                                    <option value="smoking">Smoking</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label" for="adults">Adults
                                <span class="red-asterisk"> *</span>
                            </label>
                            <div class="col-sm-9">
                                <select required class="custom-select mr-sm-2" id="adults" name="adults">
                                    <option selected value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label" for="children">Children</label>
                            <div class="col-sm-9">
                                <select class="custom-select mr-sm-2" id="children" name="children">
                                    <option selected value="0">-</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label" for="specialRequests">Special requirements</label>
                            <div class="col-sm-9">
                                <textarea rows="3" maxlength="500" id="specialRequests" name="specialRequests" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <input type="submit" class="btn btn-primary float-right"
                                       name="reservationSubmitBtn" value="Submit">
                            </div>
                        </div>
                    </form>
                    <?php } else { ?>
                        <p>Booking is reserved for customers.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal sign-in-to-book-modal" tabindex="-1" role="dialog" aria-labelledby="signInToBookModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Sign in required</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4>You have to <a href="sign-in.php">sign in</a> in order to reserve a room.</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="myProfileModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <?php if ($isSessionExists) { ?>
                            <form class="form" role="form" autocomplete="off" id="update-profile-form" method="post">
                                <input type="number" id="customerId" hidden
                                       name="customerId" value="<?php echo $cHandler->getId(); ?>" >
                                <div class="form-group">
                                    <label for="updateFullName">Full name</label>
                                    <input type="text" class="form-control" id="updateFullName"
                                           name="updateFullName" value="<?php echo $cHandler->getFullName(); ?>" >
                                </div>
                                <div class="form-group">
                                    <label for="updatePhoneNumber">Phone number</label>
                                    <input type="text" class="form-control" id="updatePhoneNumber"
                                           name="updatePhoneNumber" value="<?php echo $cHandler->getPhone(); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="updateEmail">Email</label>
                                    <input type="email" class="form-control" id="updateEmail"
                                           name="updateEmail" value="<?php echo $cHandler->getEmail(); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="updatePassword">New password</label>
                                    <input type="password" class="form-control" id="updatePassword"
                                           name="updatePassword"
                                           title="At least 4 characters with letters and numbers">
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary btn-md float-right"
                                           name="updateProfileSubmitBtn" value="Update">
                                </div>
                            </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<footer class="container">
    <p>&copy; Company 2017-2018</p>
</footer>

<script src="node_modules/jquery/dist/jquery.min.js"></script>
<script src="node_modules/popper.js/dist/popper.min.js"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
        integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
        crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.5/js/dataTables.select.min.js"></script>
<script src="js/form-submission.js"></script>
<script>
    $(document).ready(function () {
        let reservationDiv = $("#my-reservations-div");
        reservationDiv.hide();
        $(".my-reservations").click(function() {
            reservationDiv.slideToggle( "slow");
        });
        $('#myReservationsTbl').DataTable();
    });
</script>
</body>
</html>