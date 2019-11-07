import {handleRequestResponse} from '../../../../../../../ezsystems/ezplatform-admin-ui-modules/src/modules/common/helpers/request.helper';
import {showErrorNotification} from '../../../../../../../ezsystems/ezplatform-admin-ui-modules/src/modules/common/services/notification.service';

const ENDPOINT_LOAD_LIST = '/api/ezp/v2/feature-flag/list/';
const ENDPOINT_FEATURE = '/api/ezp/v2/feature-flag';

class FeatureFlagHandler {
    constructor(siteaccess, token) {
        this.siteaccess = siteaccess;
        this.token = token;
    }

    loadFeatureFlags(scope, callback) {
        const request = new Request(
            `${ENDPOINT_LOAD_LIST}${scope}`,
            {
                method: 'GET',
                mode: 'same-origin',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Siteaccess': this.siteaccess,
                    'X-CSRF-Token': this.token,
                },
            }
        );

        fetch(request)
            .then(handleRequestResponse)
            .then((data) => {
                callback(data, false, '');
            })
            .catch(showErrorNotification);
    }

    updateFeatureFlag(identifier, scope, state, callback) {
        const request = new Request(
            `${ENDPOINT_FEATURE}`,
            {
                method: 'POST',
                mode: 'same-origin',
                credentials: 'same-origin',
                body: JSON.stringify({
                    identifier, scope, state
                }),
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Siteaccess': this.siteaccess,
                    'X-CSRF-Token': this.token,
                },
            }
        );

        fetch(request)
            .then((data) => {
                callback(data, false, '');
            })
            .catch(showErrorNotification);
    }

    resetFeatureFlag(identifier, scope, callback) {
        const request = new Request(
            `${ENDPOINT_FEATURE}`,
            {
                method: 'DELETE',
                mode: 'same-origin',
                credentials: 'same-origin',
                body: JSON.stringify({
                    identifier, scope
                }),
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Siteaccess': this.siteaccess,
                    'X-CSRF-Token': this.token,
                },
            }
        );

        fetch(request)
            .then(() => {
                callback();
            })
            .catch(showErrorNotification);
    }
}

const siteaccess = document.querySelector('meta[name="SiteAccess"]').getAttribute('content');
const token = document.querySelector('meta[name="CSRF-Token"]').getAttribute('content');

export default new FeatureFlagHandler(siteaccess, token);
