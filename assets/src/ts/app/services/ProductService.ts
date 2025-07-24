import $ from 'jquery';
import { ApiResponse, WpLocalizedParams, ApiError } from '../../types';

declare const wssa_params: WpLocalizedParams;

class ProductService {
    public updateProductMeta(type: string, value: string): Promise<ApiResponse> {
        if (!wssa_params.rest_product_meta_url['update_' + type]) {
            throw new Error('No REST endpoint found for type: ' + type);
        }

        const url = wssa_params.rest_product_meta_url['update_' + type];

        return new Promise((resolve, reject) => {
            $.ajax({
                url: url,
                method: 'POST',
                beforeSend: (xhr) => {
                    xhr.setRequestHeader('X-WP-Nonce', wssa_params.nonce);
                },
                data: {
                    product_id: wssa_params.product_id,
                    value: value,
                }
            }).done((response) => {
                if (response) {
                    resolve(response as ApiResponse);
                } else {
                    reject({ message: 'Invalid API response structure.' } as ApiError);
                }
            }).fail((jqXHR) => {
                const error: ApiError = jqXHR.responseJSON || { message: 'An unknown error occurred.' };
                reject(error);
            });
        });
    }
}

export const productService = new ProductService();
