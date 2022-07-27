@include('flash-message')
<div class="card-header bg-transparent header-elements-inline">
    <h5 class="card-title">Users List</h5>
</div>
<div class="card-body">
    <div class="row mb-4">
        <div class="col">
            <table class="table table-sm table-bordered" id="usersTable" style="border-top: 1px solid lightgray;">
                <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Created</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

