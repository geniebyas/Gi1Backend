<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gi1 Info App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


    <!-- <link rel="stylesheet" href="/web/ui/css/style.css"> -->
    <link rel="stylesheet" href="../ui/css/util.css">
    <link rel="stylesheet" href="../ui/css/style.css">

</head>
<style>
    html,
    body {
        @font-face {
            font-family: Poppins-Medium;
            src: url('Poppins-Medium.ttf');
        }

        height: 100%;
        font-family:Poppins-Medium;
    }

    @font-face {
        font-family: Poppins-Medium;
        src: url('Poppins-Medium.ttf');
    }

    @font-face {
        font-family: Poppins-Medium-bold;
        font-weight: bold;
        src: url('Poppins-Medium.ttf');
    }
</style>

<body>
<div class="card m-3">
    <h5 class="card-header text-center">Delete Account</h5>
    <div class="card-body align-items-center justify-content-center">
        <div class="mb-3 col-12 mb-0">
            <div class="alert alert-warning">
                <h6 class="alert-heading mb-1">Are you sure you want to delete your account?</h6>
                <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
            </div>
        </div>
        <div class="row col-12 justify-content-center">
            <button type="button" class="btn btn-danger deactivate-account" data-toggle="modal" data-target="#exampleModal">Deactivate Account</button>
        </div>

    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Are you sure want to delete ?</h5>
                <form class="form-group" action="{{url("/deleteresp")}}" method="post">
                    <input type="email" name="email" id="confirm-delete" class="form-control mt-2" placeholder="Enter your email" required>
                    <input type="password" id="pass" name="password" class="form-control mt-2" placeholder="Enter your password" required>
                    <input type="submit" name="delete_btn" class="form-control mt-3 btn btn-success" value="Delete">
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous" defer></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous" defer></script>

</body>
</html>