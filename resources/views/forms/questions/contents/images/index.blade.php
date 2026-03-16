<div class="modal fade" id="browse-images-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab" aria-selected="true">Upload Image</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#profile" role="tab" aria-selected="false">History Images</a>
                                </li>
                                <li class="nav-item ms-auto">
                                    <i class="fas fa-close fs-5 m-2 cursor-pointer"  data-bs-dismiss="modal" aria-label="Close"></i>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane p-3 active" id="home" role="tabpanel">
                                    <input type="file" name="image" class="form-control dropify" data-upload-type="question" data-question-id="{{$question->id??null}}">
                                </div>
                                <div class="tab-pane p-3" id="profile" role="tabpanel">
                                    <p class="mb-0 text-muted">
                                        Food truck fixie locavore, accusamus mcsweeney's
                                        single-origin coffee squid. 
                                    </p>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>