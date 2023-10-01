export default class PermissionsService {
    getEmpleados() {
        return fetch('demo/data/empleados.json')
            .then((res) => res.json())
            .then((d) => d.data);
    }
}
