export interface WpLocalizedParams {
    rest_url: string;
    rest_product_meta_url: Record<string, string>;
    nonce: string;
    product_id: number;
}

export interface SeoData {
    title?: string;
    description?: string;
    shortDescription?: string;
    keywords?: string;
    summary?: string;
}

export interface agentData {
    seoData: SeoData;
    prompt: string;
}

export interface ApiResponse {
    success: string;
    data: any;
    message: string;
}

export interface ApiError {
    message: string;
}
