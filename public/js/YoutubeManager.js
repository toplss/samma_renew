class YoutubeManager {
    /**
     * @param tb_youtube index id 
     */
    constructor(tb_page) {
        this.tb_page = tb_page;
    }

    fetchYoutubeCall() {
        return $.ajax({
            url: '/api/manager/youtube_proc',
            type: 'GET',
            dataType: 'json',
            data: {'tb_page': this.tb_page, 'use_yn':'Y', 'mode':'show'}
        });
    }

    // 성공 처리 메서드
    handleSuccess(res) {
        var data = res.data;
        if (data === false) {
            console.log('요청하신 유튜브 조회 데이터가 존재하지 않습니다.');
        } else {
            //$('.'+ data.apply_area).html(data.tb_youtube_url);
            $('.'+ data.apply_area).html(data.tb_youtube_link);
        }
    }

    // 에러 처리 메서드
    handleError(jqXHR, textStatus, errorThrown) {
        console.log('youtube call error');
    }

    // 요청 실행 메서드
    execute() {
        this.fetchYoutubeCall()
            .done((data) => this.handleSuccess(data))
            .fail((jqXHR, textStatus, errorThrown) => this.handleError(jqXHR, textStatus, errorThrown));
    }
}