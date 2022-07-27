<style>
    .parsley-errors-list {
        list-style: none;
        color: red;
        padding-left: 0;
    }

    .form-control{
        display: block;
        width: 100%;
        height: calc(1.5385em + .875rem + 2px);
        padding: .4375rem .875rem;
        font-size: .8125rem;
        font-weight: 400;
        line-height: 1.5385;
        color: #333333;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ddd !important;
        border-radius: .1875rem;
        box-shadow: 0 0 0 0 transparent;
        transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }

    .form-control:focus{
        border-top-color: #007065 !important;
        border-top: 1px;
    }

     .start-stop{
         padding-left: 9px;
     }

    .borderless table {
        border-top: none;
        border-left: none;
        border-right: none;
        border-bottom: none;
    }

    .table-env td, .table-env th, .table-env tr {
        transition: background-color ease-in-out .15s;
        border: none;
    }

    .dataTables_filter input {
        padding: .4375rem .875rem;
        padding-right: 2rem;
    }
    #pipelineTable tbody tr{
        cursor: pointer;
    }
    .dataTables_paginate {
        margin: 10px 0 1.25rem 1.25rem;
    }
    .btn-padding-sm{
        padding: .2rem 0.4rem;
    }
    hr {
        margin-top: 3rem;
    }

    .tableFixHead thead th { position: sticky; top: 0; z-index: 1; }

    th, td { padding: 8px 16px; }
    th     { background:#eee; }

    .agent-selected{
        background:#d2effc;
    }

    .agent-env-selected{
        background:#e6f5fc;
    }

    .algorithm-selected{
        background:#d2effc;
    }

    .algorithm-env-selected{
        background:#e6f5fc;
    }

    .agent-list-form{
        height: 368px;
        border-bottom: 1px solid lightgray;
    }

    .custom-lock{
        font-size: larger;
    }

    .inactiveLink {
        pointer-events: none;
    }

    .disabled {
        pointer-events: all !important;
    }

    .fa-custom-size{
        font-size: 16px;
    }

    .tool-tip {
        display: inline-block;
    }

    .tool-tip [disabled] {
        pointer-events: none;
    }

    .algorithm-list-form{
        border-bottom: 1px solid lightgray;
    }

    .main-tab.nav-link.active {
        background-color: #0080FF !important;
    }

    .custom-tab .nav-link.active {
        color: #fff;
        background-color: #09093F;
        border-color: #fff;
    }

    .m-width-350{
        max-width: 350px;
    }

</style>