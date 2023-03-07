const slider = () => {
    try {
        const setSlider = (items, points = [], i = 0) => {
            items.forEach(item => item.classList.remove('active'));

            items[i].classList.add('active');

            if (points) {
                points.forEach(point => point.classList.remove('active'));

                points[i].classList.add('active');
            }
        }

        //default slider
        const sliders = document.querySelectorAll('.slider-default');

        sliders.forEach(slider => {
            const sliderItems = slider.querySelectorAll('.slider-item'),
                  sliderPoints = slider.querySelector('.slider-points');

            let points = [];

            sliderPoints && sliderItems.forEach(item => {
                const span = document.createElement('span');

                sliderPoints.append(span);
                points.push(span);
            });

            setSlider(sliderItems, points);

            let count = sliderItems.length - 1,
                j = 0;

            setInterval(() => {
                j == count ? j = 0 : j++;
                
                setSlider(sliderItems, points, j);
            }, 15000);

            points.forEach((point, i) => {
                point.addEventListener('click', () => {
                    setSlider(sliderItems, points, i);
                    j = i;
                });
            });
        });
    } catch (e) {
        console.log(e.stack);
    }
}

export default slider;