export default class PermissionsService {
    getPermisos() {
        return fetch('demo/data/permissions.json')
            .then((res) => res.json())
            .then((d) => d.data);
    }
}
