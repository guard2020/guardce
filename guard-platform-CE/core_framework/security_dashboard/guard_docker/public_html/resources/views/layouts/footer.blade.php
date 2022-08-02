<!-- Footer -->
<div class="navbar navbar-expand-lg navbar-light">
    <div class="text-center d-lg-none w-100">
        <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-footer">
            <i class="icon-unfold mr-2"></i>
            Footer
        </button>
    </div>

    <div class="navbar-collapse collapse" id="navbar-footer">
        <div class="navbar-collapse " id="footer-content">
            <div class="navbar-text">
                Copyright © {{date('Y')}} by
                <a href="https://www.mindsandsparks.org/" class="navbar-link" target="_blank">
                    MINDS&SPARKS
                </a>
            </div>

        </div>

        <ul class="navbar-nav ml-lg-auto">
            <li class="nav-item docs-item"><a href="{{ url('/docs') }}" class="navbar-nav-link" id="documentation-link" target="_blank"><i class="icon-file-text2 mr-2"></i> Docs</a></li>
        </ul>
    </div>
</div>
<!-- /footer -->

<style>
    .docs-item:hover{
        background-color: #dae0e5 !important;
    }
</style>