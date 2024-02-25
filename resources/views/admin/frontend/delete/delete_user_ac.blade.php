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
                <form class="form-group" action="{{url("https://api.gi1superapp.com/api/user/deleteuserac")}}" method="post">
                    <input type="email" name="email" id="confirm-delete" class="form-control mt-2" placeholder="Enter your email" required>
                    <input type="password" id="pass" name="password" class="form-control mt-2" placeholder="Enter your password" required>
                    <input type="submit" name="delete_btn" class="form-control mt-3 btn btn-success" value="Delete">
                </form>
            </div>
        </div>
    </div>
</div>