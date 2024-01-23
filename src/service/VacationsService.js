export default class VacationsService {

    getVacations() {
        return fetch('demo/data/vacations.json')
            .then((res) => res.json())
            .then((d) => d.data);
    }
}
