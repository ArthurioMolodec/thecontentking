<!-- Modal -->
<div id="articleModal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Article Writer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" id="articleModalBody">

                <div className="mb-2">

                    <label class="form-label mt-2">Topic</label>
                    <textarea class="form-control" id="articleTopic" rows="1" maxlength="200"></textarea>


                </div>

                <div class="mb-2">

                    <label class="form-label mt-2">Language</label>

                    <select id="articleLanguage" class="form-select">

                        <option value="">English</option>
                        <option value="Spanish">Spanish</option>
                        <option value="Chinese">Chinese</option>
                        <option value="Russian">Russian</option>
                        <option value="Korean">Korean</option>
                        <option value="Swedish">Swedish</option>
                        <option value="Italian">Italian</option>
                        <option value="German">German</option>

                    </select>

                </div>

                <div class="mb-2">

                    <label class="form-label mt-2">Tone Of Voice</label>

                    <select id="articleToneOfVoice" class="form-select">

                        <option value="">👍 Default</option>
                        <option value="casual">😸 Casual</option>
                        <option value="excited">🤩 Excited</option>
                        <option value="formal">👔 Formal</option>
                        <option value="angry">😡 Angry</option>
                        <option value="smart">🤓 Smart</option>
                        <option value="witty">😂 Witty</option>
                        <option value="neutral">😐 Neutral</option>
                        <option value="urgent">💨 Urgent</option>
                        <option value="informative">📖 Informative</option>

                    </select>

                </div>

                <div className="mb-3">

                    <label class="form-label mt-2">Outlines (One Heading / Line)</label>
                    <textarea class="form-control" id="articleOutlines" rows="6"></textarea>

                </div>

                <small class="text-danger" id="articleModalErr"></small>

            </div>



            <div class="modal-footer">
                <button type="button" class="btn grey-btn" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn app-btn" id="writeOutlines">Generate Outlines</button>
                <button type="button" class="btn app-btn" id="writeArticle">Start Writing</button>
            </div>
        </div>
    </div>
</div>