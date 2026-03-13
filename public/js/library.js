

/**
 * 첨부파일 압축 라이브러리 (public\js\compression.js)
 * Browser Image Compression
 * v2.0.2
 * by Donald <donaldcwl@gmail.com>
 * https://github.com/Donaldcwl/browser-image-compression
 * 
 */

async function compressReturnImage(input, element) {

    if (!input.files || !input.files.length) return;

    const file = input.files[0];

    const beforeSize = (file.size / 1024 / 1024).toFixed(2); // MB

    if (!file.type.startsWith('image/')) {
        validationAlertMessage('이미지 파일만 업로드 가능합니다.');
        input.value = '';
        return;
    }

    // 3MB 이하면 압축 생략
    if (file.size <= 3 * 1024 * 1024) {
        $(element).val(`${file.name} (${beforeSize}MB)`);
        return;
    }

    try {
        const options = {
        maxSizeMB: 1,
        maxWidthOrHeight: 1280,
        useWebWorker: true,
        initialQuality: 0.8,
        exifOrientation: 1
        };

        const compressedFile = await imageCompression(file, options);
        const afterSize = (compressedFile.size / 1024 / 1024).toFixed(2);

        console.log(
        `[압축 결과] ${file.name} : ${beforeSize}MB -> ${afterSize}MB`
        );

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(
        new File([compressedFile], file.name, { type: compressedFile.type })
        );
        input.files = dataTransfer.files;

        $(element).val(
        `${file.name} (${beforeSize}MB -> ${afterSize}MB)`
        );

    } catch (e) {
        console.error(e);
        validationAlertMessage('이미지 압축 중 오류가 발생했습니다.');
        input.value = '';
    }
}


